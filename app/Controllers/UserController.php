<?php

namespace App\Controllers;

use App\Models\User;
use \Exception;
use \PDO;

class UserController extends BaseController
{
    private User $userModel;
    private $db;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
        $this->userModel = new User();
    }

    public function index()
    {
        // Verifica permissão
        if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
            header('Location: /dashboard');
            exit;
        }

        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 5;
            $offset = ($page - 1) * $limit;

            $institutionId = $_SESSION['user']['institution_id'];

            $users = $this->userModel->getUsers($institutionId, $limit, $offset);
            $totalUsers = $this->userModel->getTotalUsers($institutionId);
            $totalPages = ceil($totalUsers / $limit);

            $availableRoles = $this->getRoles();

            return $this->render('users/index', [
                'users' => $users,
                'roles' => $roles,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'pageTitle' => 'Gerenciar Usuários',
            ]);
        } catch (\PDOException $e) {
            error_log("Erro na paginação dos usuários: " . $e->getMessage());
            header('Location: /users?error=' . urlencode('Erro ao carregar os usuários'));
            exit;
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $userData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'institution_id' => $_SESSION['user']['institution_id'],
                'roles' => $_POST['role_id']
            ];

            $this->userModel->create($userData);
            header('Location: /users?success=1');
        } catch (\Exception $e) {
            header('Location: /users?error=' . urlencode($e->getMessage()));
        }
        exit;
    }

    private function getRoles()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, description
                FROM roles 
                ORDER BY name ASC
            ");

            $stmt->execute();
            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!is_array($roles)) {
                error_log('Roles is not an array: ' . print_r($roles, true));
                return [];
            }

            return $roles;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar perfis: " . $e->getMessage());
            return [];
        }
    }

    // Método para buscar um único usuário pelo ID
    public function get($id)
    {
        try {
            // Validate user has permission
            if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
                http_response_code(403);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }

            // Get user data including roles
            $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.name,
                u.email,
                u.active,
                COALESCE(JSON_ARRAYAGG(
                    IF(r.id IS NOT NULL, 
                        JSON_OBJECT(
                            'role_id', ur.role_id,
                            'role_name', r.name
                        ),
                        NULL
                    )
                ), '[]') as user_roles
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = ?
            AND u.institution_id = ?
            AND u.deleted_at IS NULL
            GROUP BY u.id
            ");

            $stmt->execute([$id, $_SESSION['user']['institution_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
                exit;
            }

            // Parse user_roles JSON string to array
            $user['user_roles'] = json_decode($user['user_roles'], true);
            
            // Filter out null values that might be in the array
            if (is_array($user['user_roles'])) {
                $user['user_roles'] = array_filter($user['user_roles'], function($role) {
                    return $role !== null;
                });
                // Reset array keys
                $user['user_roles'] = array_values($user['user_roles']);
            }

            // Send JSON response
            header('Content-Type: application/json');
            echo json_encode($user);
            exit;
        } catch (\PDOException $e) {
            error_log("Error fetching user: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            exit;
        }
    }

    // Método para atualizar os dados do usuário
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $active = isset($_POST['active']) ? 1 : 0;
            $roleId = $_POST['role_id'] ?? null;

            if (!$id) {
                throw new \Exception('ID do usuário não fornecido');
            }

            // Verificar se usuário existe e pertence à instituição do usuário logado
            $institutionId = $_SESSION['user']['institution_id'];
            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ? AND institution_id = ?");
            $stmt->execute([$id, $institutionId]);

            if (!$stmt->fetch()) {
                throw new \Exception('Usuário não encontrado ou não pertence à sua instituição');
            }

            $this->db->beginTransaction();

            // Atualizar dados básicos
            if (!empty($password)) {
                $stmt = $this->db->prepare("
                UPDATE users 
                SET name = ?, email = ?, active = ?, password = ?, updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");
                $stmt->execute([
                    $name,
                    $email,
                    $active,
                    password_hash($password, PASSWORD_DEFAULT),
                    $id,
                    $institutionId
                ]);
            } else {
                $stmt = $this->db->prepare("
                UPDATE users 
                SET name = ?, email = ?, active = ?, updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");
                $stmt->execute([$name, $email, $active, $id, $institutionId]);
            }

            // Atualizar roles do usuário (primeiro remove todas e depois insere a nova)
            if ($roleId) {
                $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
                $stmt->execute([$id]);

                $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                $stmt->execute([$id, $roleId]);
            }

            $this->db->commit();
            header('Location: /users?success=1');
            exit;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            header('Location: /users?error=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
