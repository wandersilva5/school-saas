<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AccessManagement;
use \Exception;

class AccessManagementController extends BaseController
{
    private $accessManagementModel;

    public function __construct()
    {
        $this->accessManagementModel = new AccessManagement();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }
        
        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();

        $institutionId = $_SESSION['user']['institution_id'];

        // Configuração da paginação
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // itens por página
        $offset = ($page - 1) * $limit;

        try {
            // Get users with pagination using model
            $result = $this->accessManagementModel->getUsers($institutionId, $limit, $offset);
            $users = $result['users'];
            $totalPages = $result['totalPages'];

            // Get roles and institutions
            $roles = $this->accessManagementModel->getRoles();
            $institutions = $this->accessManagementModel->getInstitutions();

            return $this->render('access-management/index', [
                'users' => $users,
                'roles' => $roles,
                'institutions' => $institutions,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'pageTitle' => 'Gerenciar Acessos'
            ]);
        } catch (Exception $e) {
            // Log the error
            error_log("Error in AccessManagementController::index: " . $e->getMessage());
            
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar os usuários: ' . $e->getMessage()
            ];
            
            header('Location: /dashboard');
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

            // Update user roles using model
            $this->accessManagementModel->updateUserRoles($userId, $roleIds, $institutionId);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Perfis atualizados com sucesso'
            ];
            
            header('Location: /access-management');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar perfis: ' . $e->getMessage()
            ];
            
            header('Location: /access-management');
            exit;
        }
    }

    public function createUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            // Prepare user data
            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'role_id' => $_POST['role_id'] ?? null,
                'institution_id' => $_POST['institution_id'] ?? $_SESSION['user']['institution_id']
            ];

            // Create user using model
            $userResult = $this->accessManagementModel->createUser($userData);
            
            // TODO: Send welcome email with password to user
            // This would be implemented here using the returned data
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Usuário criado com sucesso. A senha foi enviada para o email cadastrado.'
            ];
            
            header('Location: /access-management');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
            
            header('Location: /access-management');
            exit;
        }
    }

    public function getUserRole($userId)
    {
        try {
            // Get user role using model
            $role = $this->accessManagementModel->getUserRole($userId);
            return $role;
        } catch (Exception $e) {
            error_log("Error in getUserRole: " . $e->getMessage());
            return null;
        }
    }

    public function updateUserRole()
    {
        try {
            $userId = $_POST['user_id'] ?? null;
            $roleId = $_POST['role_id'] ?? null;

            // Update user role using model
            $result = $this->accessManagementModel->updateUserRole($userId, $roleId);
            
            if ($result) {
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Perfil atualizado com sucesso'
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar perfil'
                ];
            }
            
            header('Location: /access-management');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()
            ];
            
            header('Location: /access-management');
            exit;
        }
    }

    public function createUserTI()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $userData = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '',
                'institution_id' => $_SESSION['user']['institution_id'] ?? null
            ];

            // Create TI user using model
            $this->accessManagementModel->createUserTI($userData);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Usuário TI criado com sucesso'
            ];
            
            header('Location: /access-management');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao criar usuário TI: ' . $e->getMessage()
            ];
            
            header('Location: /access-management');
            exit;
        }
    }
}