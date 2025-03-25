<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserInfo;
use \Exception;
use \PDO;

class UserController extends BaseController
{
    private User $userModel;
    private Role $roleModel;
    private UserInfo $userInfoModel;
    private $db;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->userInfoModel = new UserInfo();
    }

    public function index()
    {
        // Verifica permissão
        if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
            header('Location: /dashboard');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();

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
            // Verifica permissão
            if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
                header('Location: /dashboard');
                exit;
            }

            // Busca o usuário pelo ID
            $user = $this->userModel->get($id);
            if (!$user) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Usuário não encontrado'
                ];
                $this->redirect('/users');
            }
            
            // Obter os nomes dos perfis do usuário, não apenas IDs
            if (isset($user['roles']) && is_array($user['roles'])) {
                $roleIds = $user['roles'];
                $roleNames = [];
                foreach ($roleIds as $roleId) {
                    $role = $this->roleModel->getRoleById($roleId);
                    if ($role) {
                        $roleNames[] = $role['name'];
                    }
                }
                $user['role_names'] = $roleNames;
            }

            // Busca informações adicionais do usuário
            $userInfoModel = new UserInfo();
            $user_info = $userInfoModel->getUserInfoById($id);

            // Busca todos os perfis para o formulário de edição
            $roles = $this->getRoles();

            $this->render('users/show', [
                'pageTitle' => 'Detalhes do Usuário',
                'currentRoute' => 'users',
                'user' => $user,
                'user_info' => $user_info,
                'allRoles' => $roles
            ]);
        } catch (\PDOException $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar detalhes do usuário: ' . $e->getMessage()
            ];
            $this->redirect('/users');
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

    public function updateInfo()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
            $infoId = isset($_POST['info_id']) ? (int)$_POST['info_id'] : null;
            
            if (!$userId) {
                throw new \Exception('ID do usuário inválido');
            }

            // Verificar permissão e propriedade
            $user = $this->userModel->get($userId);
            if (!$user || $user['institution_id'] != $_SESSION['user']['institution_id']) {
                throw new \Exception('Usuário não encontrado ou acesso negado');
            }

            // Preparar dados
            $data = [
                'user_id' => $userId,
                'phone' => $_POST['phone'] ?? null,
                'cpf' => $_POST['cpf'] ?? null,
                'birth_date' => $_POST['birth_date'] ?? null,
                'gender' => $_POST['gender'] ?? null,
                'address_street' => $_POST['address_street'] ?? null,
                'address_number' => $_POST['address_number'] ?? null,
                'address_complement' => $_POST['address_complement'] ?? null,
                'address_district' => $_POST['address_district'] ?? null,
                'address_city' => $_POST['address_city'] ?? null,
                'address_state' => $_POST['address_state'] ?? null,
                'address_zipcode' => $_POST['address_zipcode'] ?? null,
                'observation' => $_POST['observation'] ?? null,
                'institution_id' => $_SESSION['user']['institution_id']
            ];

            // Instanciar modelo de informações
            $userInfoModel = new UserInfo();

            // Criar ou atualizar informações
            if ($infoId) {
                $userInfoModel->updateUserInfo($infoId, $data);
                $message = 'Informações atualizadas com sucesso';
            } else {
                $userInfoModel->createUserInfo($data);
                $message = 'Informações adicionadas com sucesso';
            }

            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => $message
            ];
            
            $this->redirect('/users/show/' . $userId);

        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar informações: ' . $e->getMessage()
            ];
            
            if (isset($userId)) {
                $this->redirect('/users/show/' . $userId);
            } else {
                $this->redirect('/users');
            }
        }
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
