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

            $roles = $this->getRoles();

            return $this->render('users/index', [
                'users' => $users,
                'allRoles' => $roles, // Alterado de 'roles' para 'allRoles'
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
            $userData = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'institution_id' => $_SESSION['user']['institution_id'],
                'roles' => $_POST['roles'] ?? []
            ];

            if ($this->userModel->create($userData)) {
                header('Location: /users?success=1');
            } else {
                throw new \Exception('Erro ao criar usuário');
            }
        } catch (\Exception $e) {
            header('Location: /users?error=' . urlencode($e->getMessage()));
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
                header('Location: /users?success=1');
            } else {
                throw new \Exception('Erro ao atualizar usuário');
            }
        } catch (\Exception $e) {
            header('Location: /users?error=' . urlencode($e->getMessage()));
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
                header('Location: /users?success=1');
            } else {
                throw new \Exception('Erro ao excluir usuário');
            }
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
}
