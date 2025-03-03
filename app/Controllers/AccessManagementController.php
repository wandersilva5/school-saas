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
        // Verifica se o usuário tem permissão
        if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
            header('Location: /dashboard');
            exit;
        }

        $institutionId = $_SESSION['user']['institution_id'];

        // Busca usuários da mesma instituição
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email, u.created_at, GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ?
            GROUP BY u.id
            ORDER BY u.name
        ");
        $stmt->execute([$institutionId]);
        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Busca roles disponíveis para a instituição
        $stmtRoles = $this->db->prepare("
            SELECT id, name, description 
            FROM roles 
            WHERE institution_id = ?
        ");
        $stmtRoles->execute([$institutionId]);
        $roles = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC);

        $roles = $this->getRoles();
        return $this->render('access-management/index', [
            'users' => $users,
            'roles' => $roles,
            'currentPage' => 'access-management',
            'pageTitle' => 'Gerenciar Acessos'
        ]);
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

        try {
            $name = $_POST['name'];
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $roleIds = $_POST['roles'] ?? [];
            $institutionId = $_SESSION['user']['institution_id'];

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
            
            $userId = $this->db->lastInsertId();

            // Associa os roles selecionados
            if (!empty($roleIds)) {
                $stmt = $this->db->prepare(
                    "INSERT INTO user_roles (user_id, role_id) 
                     SELECT ?, id FROM roles 
                     WHERE id IN (" . implode(',', array_fill(0, count($roleIds), '?')) . ")
                     AND institution_id = ?"
                );
                
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
