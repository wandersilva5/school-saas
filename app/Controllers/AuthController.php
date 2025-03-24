<?php

namespace App\Controllers;

use App\Config\Database;

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
        error_log("LOGIN METHOD CALLED: " . $_SERVER['REQUEST_METHOD']);

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
                    
                    $_SESSION['just_logged_in'] = true;
                    $roles = $_SESSION['user']['roles'];

                    switch (true) {
                        case in_array('Master', $roles):
                            header('Location: /dashboard');
                            break;
                        case in_array('Agente de controle', $roles):
                            header('Location: /home-agent');
                            break;
                        case in_array('Responsavel', $roles):
                            header('Location: /institution/list');
                            break;
                        default:
                            header('Location: /dashboard-institution');
                            break;
                    }
                    exit;
                }

                error_log("Login falhou - retornando erro");
                    $this->render('auth/login', [
                    'error' => 'Email ou senha inválidos'
                ]);

                return;

            } catch (\Exception $e) {
                error_log("Erro no login: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());

                return $this->render('auth/login', [
                    'error' => 'Erro ao realizar login: ' . $e->getMessage()
                ]);
            }
        }

        $this->render('auth/login');
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

                // Validações
                if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
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
                     domain=VALUES(domain),
                     active=1"
                );

                $institutionId = 1;

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

                $this->render('auth/register', [
                    'success' => 'Cadastro realizado com sucesso! Você já pode fazer login.',
                    'redirect' => '/login'
                ]);
            } catch (\PDOException $e) {
                $this->db->rollBack();
                error_log($e->getMessage());
                $this->render('auth/register', [
                    'error' => 'Erro ao processar o registro: ' . $e->getMessage()
                ]);
            }
        }

        $this->render('auth/register');
    }
}
