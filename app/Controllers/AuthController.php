<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\User;

class AuthController extends BaseController
{
    private $db;

    public function __construct()
    {
        try {
            $this->db = Database::getInstance()->getConnection();
        } catch (\PDOException $e) {
            die("Erro de conexão: " . $e->getMessage());
        }
    }

    public function login()
    {
        // Debug inicial
        error_log('Método login chamado. Método: ' . $_SERVER['REQUEST_METHOD']);

        // Redirect if already logged in
        if (isset($_SESSION['user'])) {
            header('Location: /dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log('Dados POST: ' . print_r($_POST, true));

            try {
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'] ?? '';

                error_log("Tentando login com email: $email");

                // Busca o usuário e seus roles
                $stmt = $this->db->prepare("
                    SELECT u.*, GROUP_CONCAT(r.name) as roles
                    FROM users u
                    LEFT JOIN user_roles ur ON u.id = ur.user_id
                    LEFT JOIN roles r ON ur.role_id = r.id
                    WHERE u.email = ?
                    GROUP BY u.id
                ");

                $stmt->execute([$email]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                error_log("Dados do usuário: " . print_r($user, true));

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'institution_id' => $user['institution_id'],
                        'roles' => $user['roles'] ? explode(',', $user['roles']) : []
                    ];

                    error_log("Sessão configurada: " . print_r($_SESSION['user'], true));
                    error_log("Redirecionando para dashboard");

                    if (in_array('Master', $_SESSION['user']['roles'])) {
                        header('Location: /dashboard');
                    } else {
                        header('Location: /dashboard-institution');
                    }
                    exit;
                }

                error_log("Login falhou - retornando erro");
                return $this->render('auth/login', [
                    'error' => 'Email ou senha inválidos'
                ]);
            } catch (\Exception $e) {
                error_log("Erro no login: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());

                return $this->render('auth/login', [
                    'error' => 'Erro ao realizar login: ' . $e->getMessage()
                ]);
            }
        }

        return $this->render('auth/login');
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function register()
    {
        // Redirect if already logged in
        if (isset($_SESSION['user'])) {
            header('Location: /dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $name = $_POST['name'] . ' ' . $_POST['surname'];
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                $password_confirm = $_POST['password_confirm'];
                $institution = htmlspecialchars($_POST['institution'] ?? '', ENT_QUOTES, 'UTF-8');

                // Validações
                if (empty($name) || empty($email) || empty($password) || empty($institution)) {
                    return $this->render('auth/register', ['error' => 'Todos os campos são obrigatórios']);
                }

                if ($password !== $password_confirm) {
                    return $this->render('auth/register', ['error' => 'As senhas não conferem']);
                }

                $this->db->beginTransaction();

                // 1. Insere ou obtém a instituição
                $stmtInst = $this->db->prepare(
                    "INSERT INTO institutions (name, domain, logo_url, active) 
                     VALUES (?, ?, ?, 1) 
                     ON DUPLICATE KEY UPDATE 
                     id=LAST_INSERT_ID(id),
                     name=VALUES(name),
                     domain=VALUES(domain)
                     active=1"
                );

                // Verifica se já existe uma instituição com este nome
                $stmtCheckInst = $this->db->prepare("SELECT id FROM institutions WHERE name = ?");
                $stmtCheckInst->execute([$institution]);
                $existingInst = $stmtCheckInst->fetch();

                if (!$existingInst) {
                    // Só insere se não existir
                    $domain = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $institution)) . '.com.br';

                    $stmtInst->execute([
                        $institution,  // nome da instituição do formulário
                        $domain,      // domínio gerado
                        null          // logo_url
                    ]);

                    $institutionId = $this->db->lastInsertId();
                } else {
                    $institutionId = $existingInst['id'];
                }

                // 2. Insere ou obtém o papel de TI para esta instituição
                $stmtRole = $this->db->prepare(
                    "INSERT INTO roles (name, description, institution_id, created_at) 
                     VALUES ('TI', 'Administrador de TI', ?, NOW()) 
                     ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)"
                );
                $stmtRole->execute([$institutionId]);
                $roleId = $this->db->lastInsertId();

                // 3. Insere o usuário
                $stmt = $this->db->prepare(
                    "INSERT INTO users (name, email, password, institution_id, created_at) 
                     VALUES (?, ?, ?, ?, NOW())"
                );

                $stmt->execute([
                    $name,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $institutionId
                ]);

                $userId = $this->db->lastInsertId();

                // 4. Associa o usuário ao papel de TI
                $stmtUserRole = $this->db->prepare(
                    "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
                );
                $stmtUserRole->execute([$userId, $roleId]);

                $this->db->commit();

                return $this->render('auth/register', [
                    'success' => 'Cadastro realizado com sucesso! Você já pode fazer login.',
                    'redirect' => '/login'
                ]);
            } catch (\PDOException $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                return $this->render('auth/register', [
                    'error' => 'Erro ao processar o registro: ' . $e->getMessage()
                ]);
            }
        }

        return $this->render('auth/register');
    }
}
