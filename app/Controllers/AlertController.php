<?php

namespace App\Controllers;

use App\Models\Alert;
use App\Models\Role;

class AlertController extends BaseController
{
    private $alertModel;
    private $roleModel;

    public function __construct()
    {
        $this->alertModel = new Alert();
        $this->roleModel = new Role();
    }

    /**
     * Display the list of alerts
     */
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alert: User not in session");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();
        
        // Verify user has Secretaria role
        $hasSecretariaRole = false;
        if (isset($_SESSION['user']['roles']) && is_array($_SESSION['user']['roles'])) {
            $hasSecretariaRole = in_array('Secretaria', $_SESSION['user']['roles']);
        }
        
        $institutionId = $_SESSION['user']['institution_id'];

        // Pagination setup
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // items per page
        $offset = ($page - 1) * $limit;

        try {
            // Get alerts for the institution
            $alerts = $this->alertModel->getAlerts($institutionId, $limit, $offset);
            $totalAlerts = $this->alertModel->getTotalAlerts($institutionId);
            $totalPages = ceil($totalAlerts / $limit);
            
            // Get roles for dropdown
            $roles = $this->alertModel->getRoles($institutionId);

            $this->render('alerts/index', [
                'pageTitle' => 'Gerenciar Alertas',
                'alerts' => $alerts,
                'roles' => $roles,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'currentSection' => 'alerts',
                'hasSecretariaRole' => $hasSecretariaRole
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar alertas: ' . $e->getMessage()
            ];

            $this->render('alerts/index', [
                'pageTitle' => 'Gerenciar Alertas',
                'alerts' => [],
                'roles' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'currentSection' => 'alerts',
                'hasSecretariaRole' => $hasSecretariaRole
            ]);
        }
    }

    /**
     * Display user's alerts (for notifications)
     */
    public function userAlerts()
    {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        try {
            $userId = $_SESSION['user']['id'];
            $institutionId = $_SESSION['user']['institution_id'];
            
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
            
            $alerts = $this->alertModel->getAlertsForUser($userId, $institutionId, $limit, $offset);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'alerts' => $alerts,
                'count' => count($alerts)
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Store a new alert
     */
    public function store()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
            exit;
        }

        // Verify user has Secretaria role
        $hasSecretariaRole = false;
        if (isset($_SESSION['user']['roles']) && is_array($_SESSION['user']['roles'])) {
            $hasSecretariaRole = in_array('Secretaria', $_SESSION['user']['roles']);
        }
        
        if (!$hasSecretariaRole) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Você não tem permissão para criar alertas'
            ];
            $this->redirect('/alerts');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];
                $userId = $_SESSION['user']['id'];

                // Form data validation
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $priority = trim($_POST['priority'] ?? 'normal');
                $targetRoles = isset($_POST['target_roles']) ? implode(',', $_POST['target_roles']) : 'all';
                $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
                $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

                if (empty($title)) {
                    throw new \Exception('O título do alerta é obrigatório');
                }

                if (empty($message)) {
                    throw new \Exception('A mensagem do alerta é obrigatória');
                }

                $data = [
                    'title' => $title,
                    'message' => $message,
                    'priority' => $priority,
                    'target_roles' => $targetRoles,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'created_by' => $userId,
                    'institution_id' => $institutionId
                ];

                $this->alertModel->create($data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Alerta criado com sucesso!'
                ];
                $this->redirect('/alerts');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao criar alerta: ' . $e->getMessage()
                ];
                $this->redirect('/alerts');
            }
        } else {
            $this->redirect('/alerts');
        }
    }

    /**
     * Update an existing alert
     */
    public function update($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
            exit;
        }

        // Verify user has Secretaria role
        $hasSecretariaRole = false;
        if (isset($_SESSION['user']['roles']) && is_array($_SESSION['user']['roles'])) {
            $hasSecretariaRole = in_array('Secretaria', $_SESSION['user']['roles']);
        }
        
        if (!$hasSecretariaRole) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Você não tem permissão para atualizar alertas'
            ];
            $this->redirect('/alerts');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if alert exists
                $alert = $this->alertModel->getAlertById($id, $institutionId);
                if (!$alert) {
                    throw new \Exception('Alerta não encontrado');
                }

                // Form data validation
                $title = trim($_POST['title'] ?? '');
                $message = trim($_POST['message'] ?? '');
                $priority = trim($_POST['priority'] ?? 'normal');
                $targetRoles = isset($_POST['target_roles']) ? implode(',', $_POST['target_roles']) : 'all';
                $startDate = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
                $endDate = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

                if (empty($title)) {
                    throw new \Exception('O título do alerta é obrigatório');
                }

                if (empty($message)) {
                    throw new \Exception('A mensagem do alerta é obrigatória');
                }

                $data = [
                    'title' => $title,
                    'message' => $message,
                    'priority' => $priority,
                    'target_roles' => $targetRoles,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'institution_id' => $institutionId
                ];

                $this->alertModel->update($id, $data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Alerta atualizado com sucesso!'
                ];
                
                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/alerts');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar alerta: ' . $e->getMessage()
                ];
                
                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/alerts');
            }
        } else {
            $this->redirect('/alerts');
        }
    }

    /**
     * Delete an alert
     */
    public function delete($id)
    {
        if (!isset($_SESSION['user'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            $this->redirect('/login');
            exit;
        }

        // Verify user has Secretaria role
        $hasSecretariaRole = false;
        if (isset($_SESSION['user']['roles']) && is_array($_SESSION['user']['roles'])) {
            $hasSecretariaRole = in_array('Secretaria', $_SESSION['user']['roles']);
        }
        
        if (!$hasSecretariaRole) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['error' => 'Você não tem permissão para excluir alertas']);
                exit;
            }
            
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Você não tem permissão para excluir alertas'
            ];
            $this->redirect('/alerts');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if alert exists
                $alert = $this->alertModel->getAlertById($id, $institutionId);
                if (!$alert) {
                    throw new \Exception('Alerta não encontrado');
                }

                $this->alertModel->delete($id, $institutionId);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Alerta excluído com sucesso!'
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/alerts');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao excluir alerta: ' . $e->getMessage()
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/alerts');
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
                exit;
            }
            $this->redirect('/alerts');
        }
    }

    /**
     * Get alert by ID for AJAX requests
     */
    public function getById()
    {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            try {
                $id = $_GET['id'];
                $institutionId = $_SESSION['user']['institution_id'];

                $alert = $this->alertModel->getAlertById($id, $institutionId);

                if (!$alert) {
                    header('Content-Type: application/json');
                    http_response_code(404);
                    echo json_encode(['error' => 'Alerta não encontrado']);
                    exit;
                }

                // Get selected roles as array
                $alert['selected_roles'] = !empty($alert['target_roles']) ? explode(',', $alert['target_roles']) : [];

                header('Content-Type: application/json');
                echo json_encode($alert);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }
    
    /**
     * Get active alerts for the user
     */
    public function getActiveAlerts()
    {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    
        try {
            $userId = $_SESSION['user']['id'];
            $institutionId = $_SESSION['user']['institution_id'];
    
            $alerts = $this->alertModel->getActiveAlerts($userId, $institutionId);
    
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'alerts' => $alerts
            ]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
}
