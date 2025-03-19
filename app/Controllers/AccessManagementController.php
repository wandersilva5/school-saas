<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use \Exception;
use \PDO;

class AccessManagementController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }

    public function index()
    {
        // Debug
        error_log("DashboardController: Verificando autenticação");
        error_log("DashboardController: Sessão atual: " . print_r($_SESSION, true));

        if (!isset($_SESSION['user'])) {
            error_log("DashboardController: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        $institutionId = $_SESSION['user']['institution_id'];

        // Configuração da paginação
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // itens por página
        $offset = ($page - 1) * $limit;

        try {
            // Conta total de registros
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT u.id) as total
                FROM users u
                LEFT JOIN institutions i ON u.institution_id = i.id
                WHERE u.institution_id = ?
            ");
            $stmt->execute([$institutionId]);
            $totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalUsers / $limit);

            // Busca usuários da mesma instituição com paginação
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.created_at, 
                    i.name as institution_name, 
                    GROUP_CONCAT(r.name) as roles
                FROM users u
                LEFT JOIN institutions i ON u.institution_id = i.id
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.institution_id = ?
                GROUP BY u.id, u.name, u.email, u.created_at, i.name
                ORDER BY u.created_at DESC
                LIMIT ? OFFSET ?
            ");

            $stmt->execute([$institutionId, $limit, $offset]);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Busca todas as instituições ativas
            $stmtInstitutions = $this->db->prepare("
                SELECT id, name 
                FROM institutions 
                WHERE deleted_at IS NULL
                ORDER BY name
            ");
            $stmtInstitutions->execute();
            $institutions = $stmtInstitutions->fetchAll(\PDO::FETCH_ASSOC);

            $roles = $this->getRoles();

            return $this->render('access-management/index', [
                'users' => $users,
                'roles' => $roles,
                'institutions' => $institutions,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'pageTitle' => 'Gerenciar Acessos'
            ]);
        } catch (\PDOException $e) {
            // Log do erro
            error_log("Erro na paginação: " . $e->getMessage());
            // Redireciona com mensagem de erro
            header('Location: /access-management?error=' . urlencode('Erro ao carregar os usuários'));
            exit;
        }
    }

    public function updateUserRoles()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $userId = $_POST['user_id'] ?? null;
            $roleIds = $_POST['roles'] ?? [];
            $institutionId = $_SESSION['user']['institution_id'];

            // Verifica se o usuário pertence à mesma instituição
            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ? AND institution_id = ?");
            $stmt->execute([$userId, $institutionId]);
            if (!$stmt->fetch()) {
                throw new \Exception('Usuário não encontrado ou não pertence à sua instituição');
            }

            $this->db->beginTransaction();

            // Remove roles existentes
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Adiciona novos roles
            if (!empty($roleIds)) {
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id) 
                    SELECT ?, id FROM roles 
                    WHERE id IN (" . implode(',', array_fill(0, count($roleIds), '?')) . ")
                    AND institution_id = ?
                ");

                $params = array_merge([$userId], $roleIds, [$institutionId]);
                $stmt->execute($params);
            }

            $this->db->commit();
            header('Location: /access-management?success=1');
        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /access-management?error=' . urlencode($e->getMessage()));
        }
    }

    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $transactionStarted = false;

        try {
            // Validação dos campos
            $name = trim($_POST['name'] ?? '');
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $roleId = $_POST['role_id'] ?? null;
            $institutionId = $_POST['institution_id'] ?? $_SESSION['user']['institution_id'];

            // Validações
            if (empty($name)) {
                throw new \Exception('O nome é obrigatório');
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email inválido');
            }

            if (empty($roleId)) {
                throw new \Exception('Selecione pelo menos um perfil');
            }

            // Verifica se o email já está em uso
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new \Exception('Este email já está em uso');
            }

            // Gera uma senha aleatória
            $password = bin2hex(random_bytes(8));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $this->db->beginTransaction();
            $transactionStarted = true;

            // Insere o usuário
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, email, password, institution_id, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                $institutionId
            ]);

            $userId = $this->db->lastInsertId();

            // Associa o role ao usuário
            $stmt = $this->db->prepare(
                "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
            );
            $stmt->execute([$userId, $roleId]);

            $this->db->commit();

            // TODO: Enviar email com a senha para o usuário

            header('Location: /access-management?success=1');
            exit;
        } catch (\Exception $e) {
            if ($transactionStarted) {
                $this->db->rollBack();
            }
            header('Location: /access-management?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function getRoles()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, description 
                FROM roles 
                ORDER BY name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar roles: " . $e->getMessage());
            return [];
        }
    }

    public function getUserRole($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT role_id 
                FROM user_roles 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erro ao buscar role do usuário: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserRole()
    {
        try {
            $userId = $_POST['user_id'] ?? null;
            $roleId = $_POST['role_id'] ?? null;

            if (!$userId || !$roleId) {
                return ['success' => false, 'message' => 'Dados inválidos'];
            }

            // Primeiro remove roles existentes
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Insere novo role
            $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$userId, $roleId]);

            return ['success' => true, 'message' => 'Perfil atualizado com sucesso'];
        } catch (Exception $e) {
            error_log("Erro ao atualizar role: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erro ao atualizar perfil'];
        }
    }

    public function createUserTI()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $name = $_POST['name'];
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $roleId = 'TI';
            $institutionId = $_SESSION['institution_id'];

            $this->db->beginTransaction();

            // Insere o usuário
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

            $this->db->commit();
            header('Location: /access-management?success=1');
        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /access-management?error=' . urlencode($e->getMessage()));
        }
    }
}
