<?php

namespace App\Controllers;

use App\Models\Institution;
use \Exception;

class InstitutionController extends BaseController
{
    private $institutionModel;

    public function __construct()
    {
        $this->institutionModel = new Institution();
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

        try {
            // Configuração da paginação
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10; // itens por página
            $offset = ($page - 1) * $limit;

            // Buscar instituições com paginação
            $institutions = $this->institutionModel->getInstitutions($limit, $offset);
            $totalInstitutions = $this->institutionModel->getTotalInstitutions();
            $totalPages = ceil($totalInstitutions / $limit);

            return $this->render('institution/index', [
                'institutions' => $institutions,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'pageTitle' => 'Gerenciar Instituições'
            ]);
            
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar instituições: ' . $e->getMessage()
            ];
            
            return $this->render('institution/index', [
                'institutions' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'pageTitle' => 'Gerenciar Instituições'
            ]);
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            // Validação dos campos
            $name = $_POST['name'];
            $domain = $_POST['domain'];
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = $_POST['phone'];
            $nameContact = $_POST['name_contact'];
            
            // Process logo upload if provided
            $logoUrl = '';
            if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === UPLOAD_ERR_OK) {
                $logoUrl = $this->institutionModel->uploadLogo($_FILES['logo_url']);
            }

            // Prepare data for model
            $data = [
                'name' => $name,
                'domain' => $domain,
                'logo_url' => $logoUrl,
                'email' => $email,
                'phone' => $phone,
                'name_contact' => $nameContact
            ];

            // Create institution using model
            $this->institutionModel->create($data);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Instituição criada com sucesso'
            ];
            
            header('Location: /institution');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao criar instituição: ' . $e->getMessage()
            ];
            
            header('Location: /institution');
            exit;
        }
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            // Validação dos campos
            $name = $_POST['name'];
            $domain = $_POST['domain'];
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $phone = $_POST['phone'];
            $nameContact = $_POST['name_contact'];
            
            // Process logo - use existing or upload new
            $logoUrl = $_POST['existing_logo_url'];
            if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === UPLOAD_ERR_OK) {
                $logoUrl = $this->institutionModel->uploadLogo($_FILES['logo_url']);
            }
            
            // Prepare data for model
            $data = [
                'name' => $name,
                'domain' => $domain,
                'logo_url' => $logoUrl,
                'email' => $email,
                'phone' => $phone,
                'name_contact' => $nameContact
            ];

            // Update institution using model
            $this->institutionModel->update($id, $data);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Instituição atualizada com sucesso'
            ];
            
            header('Location: /institution');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar instituição: ' . $e->getMessage()
            ];
            
            header('Location: /institution');
            exit;
        }
    }

    public function delete($id)
    {
        try {
            $this->institutionModel->delete($id);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Instituição removida com sucesso'
            ];
            
            header('Location: /institution');
            exit;
        } catch (Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao remover instituição: ' . $e->getMessage()
            ];
            
            header('Location: /institution');
            exit;
        }
    }

    public function list()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user']['id'];

        try {
            // Get institutions where user has children using model
            $institutions = $this->institutionModel->getInstitutionsForGuardian($userId);
            
            // Render without the main layout by directly outputting
            // instead of using $this->render()
            require_once __DIR__ . '/../Views/institution/list.php';
            exit;
        } catch (Exception $e) {
            error_log("Error in list: " . $e->getMessage());
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar instituições: ' . $e->getMessage()
            ];
            header('Location: /login');
            exit;
        }
    }

    public function select($id)
    {
        // Debug
        error_log("InstitutionController::select called with ID: " . $id);

        // Verify user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        // Verify user is Responsavel
        if (!in_array('Responsavel', $_SESSION['user']['roles'])) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Acesso negado'
            ];
            header('Location: /login');
            exit;
        }

        try {
            // Verify institution exists and user has access using model
            $hasAccess = $this->institutionModel->verifyGuardianAccess($_SESSION['user']['id'], $id);
            
            if (!$hasAccess) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Instituição não encontrada ou acesso negado'
                ];
                header('Location: /institution/list');
                exit;
            }

            // Set selected institution in session
            $_SESSION['user']['institution_id'] = $id;

            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Instituição selecionada com sucesso'
            ];

            // Debug
            error_log("Institution selected successfully, redirecting to dashboard");

            // Redirect to dashboard
            header('Location: /dashboard-institution');
            exit;
        } catch (Exception $e) {
            error_log("Error in select: " . $e->getMessage());
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao selecionar instituição: ' . $e->getMessage()
            ];
            header('Location: /institution/list');
            exit;
        }
    }
}