<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;
use \Exception;
use \PDO;

class UserController extends BaseController
{
    private User $userModel;
    private Role $roleModel;
    private $db;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
        $this->userModel = new User();
        $this->roleModel = new Role();
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

            $roles = $this->getRoles();

            $this->render('users/index', [
                'users' => $users,
                'allRoles' => $roles, // Alterado de 'roles' para 'allRoles'
                'currentPage' => $page,
                'currentRoute' => 'users',
                'totalPages' => $totalPages,
                'pageTitle' => 'Gerenciar Usuários',
            ]);
            error_log("After render in UserController::index - Should not see this if render exits");
        } catch (\PDOException $e) {
            $_SESSION['toast'] = [
                'type' => 'error',  // or 'error', 'warning', 'info'
                'message' => 'Erro ao carregar os usuários ' . $e->getMessage()
            ];
        }
    }

    public function show($id)
    {
        try {
            if (!$id || !is_numeric($id)) {
                throw new \Exception('ID inválido');
            }

            if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
                throw new \Exception('Requisição inválida');
            }

            $user = $this->userModel->get($id);
            if (!$user) {
                throw new \Exception('Usuário não encontrado');
            }

            if ($user['institution_id'] != $_SESSION['user']['institution_id']) {
                throw new \Exception('Acesso negado');
            }

            // Remove sensitive data
            unset($user['password']);

            header('Content-Type: application/json');
            echo json_encode($user);
            exit;
        } catch (\Exception $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
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
            // Validate required fields
            if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
                throw new \Exception('Nome, email e senha são obrigatórios');
            }

            // Check email format
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Formato de email inválido');
            }

            // Create user data array
            $userData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'institution_id' => $_SESSION['user']['institution_id'],
                'roles' => $_POST['roles'] ?? []
            ];

            // Add logging for debugging
            error_log("Creating user with data: " . print_r($userData, true));

            // Try to create the user
            if ($this->userModel->create($userData)) {
                $_SESSION['toast'] = [
                    'type' => 'success',  // or 'error', 'warning', 'info'
                    'message' => 'Operação realizada com sucesso!'
                ];
            } else {
                throw new \Exception('Erro ao criar usuário - possível duplicidade de email');
            }
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',  // or 'error', 'warning', 'info'
                'message' => 'Erro ao carregar os usuários ' . $e->getMessage()
            ];
        }
        exit;
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $userData = [
                'id' => $id,
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'] ?: null,
                'active' => isset($_POST['active']) ? 1 : 0,
                'roles' => $_POST['roles'] ?? [],
                'institution_id' => $_SESSION['user']['institution_id']
            ];

            if ($this->userModel->update($userData)) {
                $_SESSION['toast'] = [
                    'type' => 'success',  // or 'error', 'warning', 'info'
                    'message' => 'Operação realizada com sucesso! O usuário foi atualizado.'
                ];
            } else {
                throw new \Exception('Erro ao atualizar usuário'); // Make sure this is "atualizar", not "criar"
            }
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',  // or 'error', 'warning', 'info'
                'message' => 'Erro ao atualizar usuário ' . $e->getMessage()
            ];
        }
        exit;
    }

    public function delete($id)
    {
        try {
            $user = $this->userModel->get($id);

            if (!$user || $user['institution_id'] != $_SESSION['user']['institution_id']) {
                throw new \Exception('Usuário não encontrado ou acesso negado');
            }

            if ($this->userModel->delete($id)) {
                $_SESSION['toast'] = [
                    'type' => 'success',  // or 'error', 'warning', 'info'
                    'message' => 'Operação realizada com sucesso! O usuário foi deletado.'
                ];
            } else {
                throw new \Exception('Erro ao excluir usuário');
            }
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',  // or 'error', 'warning', 'info'
                'message' => 'Erro ao excluir ou desativar usuário ' . $e->getMessage()
            ];
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
}
