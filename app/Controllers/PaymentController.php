<?php

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Institution;

class PaymentController extends BaseController
{
    private $paymentModel;
    private $studentModel;
    private $institutionModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
        $this->studentModel = new Student();
        $this->institutionModel = new Institution();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alert: User not in session");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();

        $institutionId = $_SESSION['user']['institution_id'];

        // Pagination setup
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 7; // items per page
        $offset = ($page - 1) * $limit;

        // Filters
        $filters = [
            'status' => $_GET['status'] ?? '',
            'student_id' => $_GET['student_id'] ?? '',
            'due_date_from' => $_GET['due_date_from'] ?? '',
            'due_date_to' => $_GET['due_date_to'] ?? ''
        ];

        try {
            // Get payments with pagination and filters
            $payments = $this->paymentModel->getPayments($institutionId, $limit, $offset, $filters);
            $totalPayments = $this->paymentModel->getTotalPayments($institutionId, $filters);
            $totalPages = ceil($totalPayments / $limit);

            // Get students for dropdown filter
            $students = $this->paymentModel->getStudentsForPayment($institutionId);

            // Get payment statistics for the dashboard cards
            $paymentStats = $this->paymentModel->getPaymentStats($institutionId);

            return $this->render('payments/index', [
                'pageTitle' => 'Gerenciamento de Pagamentos',
                'payments' => $payments,
                'students' => $students,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filters' => $filters,
                'stats' => $paymentStats,
                'currentSection' => 'payments'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar pagamentos: ' . $e->getMessage()
            ];

            return $this->render('payments/index', [
                'pageTitle' => 'Gerenciamento de Pagamentos',
                'payments' => [],
                'students' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'filters' => $filters,
                'stats' => [],
                'currentSection' => 'payments'
            ]);
        }
    }

    /**
     * Display a single payment
     */
    public function show($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $payment = $this->paymentModel->getPaymentById($id, $institutionId);

            if (!$payment) {
                throw new \Exception('Pagamento não encontrado');
            }

            // Adiciona dados formatados para a view
            $payment['formatted_amount'] = number_format($payment['amount'], 2, ',', '.');
            $payment['formatted_due_date'] = date('d/m/Y', strtotime($payment['due_date']));
            if ($payment['payment_date']) {
                $payment['formatted_payment_date'] = date('d/m/Y', strtotime($payment['payment_date']));
            }

            $this->render('payments/show', [
                'pageTitle' => 'Detalhes do Pagamento',
                'payment' => $payment,
                'currentSection' => 'payments'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/payments');
        }
    }

    /**
     * Store a new payment
     */
    public function store()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Form data validation and conversion
                $amount = str_replace(['.', ','], ['', '.'], $_POST['amount']);
                $discountAmount = str_replace(['.', ','], ['', '.'], $_POST['discount_amount'] ?? '0,00');
                $fineAmount = str_replace(['.', ','], ['', '.'], $_POST['fine_amount'] ?? '0,00');

                $data = [
                    'student_id' => $_POST['student_id'],
                    'amount' => floatval($amount),
                    'description' => $_POST['description'],
                    'due_date' => $_POST['due_date'],
                    'reference_month' => $_POST['reference_month'],
                    'reference_year' => $_POST['reference_year'],
                    'status' => 'Pendente',
                    'discount_amount' => floatval($discountAmount),
                    'fine_amount' => floatval($fineAmount),
                    'notes' => $_POST['notes'] ?? null,
                    'institution_id' => $institutionId
                ];

                // Create payment
                $paymentId = $this->paymentModel->createPayment($data);

                // Generate boleto if option was selected
                if (isset($_POST['generate_boleto']) && $_POST['generate_boleto'] == 1) {
                    try {
                        $boletoCode = $this->paymentModel->generateBoleto($paymentId, $institutionId);
                        $_SESSION['toast'] = [
                            'type' => 'success',
                            'message' => 'Pagamento criado e boleto gerado com sucesso!'
                        ];
                    } catch (\Exception $e) {
                        $_SESSION['toast'] = [
                            'type' => 'warning',
                            'message' => 'Pagamento criado, mas houve um erro ao gerar o boleto: ' . $e->getMessage()
                        ];
                    }
                } else {
                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => 'Pagamento criado com sucesso!'
                    ];
                }

                $this->redirect('/payments');
            } catch (\Exception $e) {
                error_log('Error creating payment: ' . $e->getMessage());
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao criar pagamento: ' . $e->getMessage()
                ];
                $this->redirect('/payments/create');
            }   
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Método inválido'
            ];
            $this->redirect('/payments/create');
        }
    }

    /**
     * Display the form to create a new payment
     */
    public function create()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Get all active students for the dropdown
            $students = $this->paymentModel->getStudentsForPayment($institutionId);

            $this->render('payments/create', [
                'pageTitle' => 'Criar Novo Pagamento',
                'students' => $students,
                'currentSection' => 'payments'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/payments');
        }
    }

    /**
     * Display the form to edit a payment
     */
    public function edit($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $payment = $this->paymentModel->getPaymentById($id, $institutionId);

            if (!$payment) {
                throw new \Exception('Pagamento não encontrado');
            }

            // Get all active students for the dropdown
            $students = $this->paymentModel->getStudentsForPayment($institutionId);

            $this->render('payments/edit', [
                'pageTitle' => 'Editar Pagamento',
                'payment' => $payment,
                'students' => $students,
                'currentSection' => 'payments'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/payments');
        }
    }

    /**
     * Update an existing payment
     */
    public function update($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if payment exists
                $payment = $this->paymentModel->getPaymentById($id, $institutionId);
                if (!$payment) {
                    throw new \Exception('Pagamento não encontrado');
                }

                // Form data validation
                $studentId = $_POST['student_id'] ?? null;
                $amount = $_POST['amount'] ?? null;
                $description = $_POST['description'] ?? '';
                $dueDate = $_POST['due_date'] ?? null;
                $referenceMonth = $_POST['reference_month'] ?? null;
                $referenceYear = $_POST['reference_year'] ?? null;
                $status = $_POST['status'] ?? 'Pendente';

                if (empty($studentId)) {
                    throw new \Exception('O aluno é obrigatório');
                }

                if (empty($amount) || !is_numeric($amount)) {
                    throw new \Exception('Valor inválido');
                }

                if (empty($dueDate)) {
                    throw new \Exception('A data de vencimento é obrigatória');
                }

                if (empty($referenceMonth) || empty($referenceYear)) {
                    throw new \Exception('Mês e ano de referência são obrigatórios');
                }

                // Prepare data
                $data = [
                    'student_id' => $studentId,
                    'amount' => $amount,
                    'description' => $description,
                    'due_date' => $dueDate,
                    'reference_month' => $referenceMonth,
                    'reference_year' => $referenceYear,
                    'status' => $status,
                    'boleto_code' => $_POST['boleto_code'] ?? null,
                    'discount_amount' => $_POST['discount_amount'] ?? 0,
                    'fine_amount' => $_POST['fine_amount'] ?? 0,
                    'payment_method' => $_POST['payment_method'] ?? null,
                    'notes' => $_POST['notes'] ?? null,
                    'institution_id' => $institutionId
                ];

                // Update payment
                $this->paymentModel->updatePayment($id, $data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Pagamento atualizado com sucesso!'
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/payments');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao atualizar pagamento: ' . $e->getMessage()
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/payments/edit/' . $id);
            }
        } else {
            $this->redirect('/payments');
        }
    }

    /**
     * Delete a payment
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
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if payment exists
                $payment = $this->paymentModel->getPaymentById($id, $institutionId);
                if (!$payment) {
                    throw new \Exception('Pagamento não encontrado');
                }

                // Don't allow deletion of paid payments
                if ($payment['status'] === 'Pago') {
                    throw new \Exception('Não é possível excluir um pagamento já realizado');
                }

                $this->paymentModel->deletePayment($id, $institutionId);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Pagamento excluído com sucesso!'
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/payments');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao excluir pagamento: ' . $e->getMessage()
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/payments');
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
                exit;
            }
            $this->redirect('/payments');
        }
    }

    /**
     * Mark a payment as paid
     */
    public function markAsPaid($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if payment exists
                $payment = $this->paymentModel->getPaymentById($id, $institutionId);
                if (!$payment) {
                    throw new \Exception('Pagamento não encontrado');
                }

                // Don't allow marking already paid payments
                if ($payment['status'] === 'Pago') {
                    throw new \Exception('Este pagamento já está marcado como pago');
                }

                // Form data validation
                $paymentMethod = $_POST['payment_method'] ?? null;
                $paymentAmount = $_POST['payment_amount'] ?? $payment['amount'];
                $paymentDate = $_POST['payment_date'] ?? date('Y-m-d');

                if (empty($paymentMethod)) {
                    throw new \Exception('O método de pagamento é obrigatório');
                }

                if (empty($paymentAmount) || !is_numeric($paymentAmount)) {
                    throw new \Exception('Valor de pagamento inválido');
                }

                // Prepare data
                $data = [
                    'payment_method' => $paymentMethod,
                    'payment_amount' => $paymentAmount,
                    'payment_date' => $paymentDate,
                    'institution_id' => $institutionId
                ];

                // Mark as paid
                $this->paymentModel->markAsPaid($id, $data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Pagamento registrado com sucesso!'
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/payments');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao registrar pagamento: ' . $e->getMessage()
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/payments');
            }
        } else {
            $this->redirect('/payments');
        }
    }

    /**
     * Generate a boleto for a payment
     */
    public function generateBoleto($id)
    {
        // Prevent any output before JSON
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (!isset($_SESSION['user'])) {
                throw new \Exception('Sessão expirada');
            }

            $institutionId = $_SESSION['user']['institution_id'];
            
            // Check if payment exists
            $payment = $this->paymentModel->getPaymentById($id, $institutionId);
            if (!$payment) {
                throw new \Exception('Pagamento não encontrado');
            }

            if ($payment['status'] === 'Pago') {
                throw new \Exception('Não é possível gerar boleto para um pagamento já realizado');
            }

            $result = $this->paymentModel->generateBoleto($id, $institutionId);
            
            echo json_encode([
                'success' => true,
                'boleto_code' => $result['code'],
                'boleto_url' => $result['url']
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Display the page to generate monthly payments
     */
    public function batchGenerate()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];

            $this->render('payments/batch-generate', [
                'pageTitle' => 'Gerar Mensalidades em Lote',
                'currentSection' => 'payments'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/payments');
        }
    }

    /**
     * Process the batch generation of monthly payments
     */
    public function batchGenerateProcess()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Form data validation
                $amount = $_POST['amount'] ?? null;
                $description = $_POST['description'] ?? '';
                $dueDate = $_POST['due_date'] ?? null;
                $referenceMonth = $_POST['reference_month'] ?? null;
                $referenceYear = $_POST['reference_year'] ?? null;

                if (empty($amount) || !is_numeric($amount)) {
                    throw new \Exception('Valor inválido');
                }

                if (empty($dueDate)) {
                    throw new \Exception('A data de vencimento é obrigatória');
                }

                if (empty($referenceMonth) || empty($referenceYear)) {
                    throw new \Exception('Mês e ano de referência são obrigatórios');
                }

                // Prepare data
                $data = [
                    'amount' => $amount,
                    'description' => $description,
                    'due_date' => $dueDate,
                    'reference_month' => $referenceMonth,
                    'reference_year' => $referenceYear,
                    'discount_amount' => $_POST['discount_amount'] ?? 0
                ];

                // Generate monthly payments
                $result = $this->paymentModel->generateMonthlyPayments($institutionId, $data);

                if ($result['success']) {
                    $_SESSION['toast'] = [
                        'type' => 'success',
                        'message' => $result['count'] . ' mensalidades geradas com sucesso!'
                    ];

                    if (!empty($result['errors'])) {
                        $_SESSION['errors'] = $result['errors'];
                    }
                } else {
                    throw new \Exception('Erro ao gerar mensalidades');
                }

                $this->redirect('/payments');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao gerar mensalidades: ' . $e->getMessage()
                ];
                $this->redirect('/payments/batch-generate');
            }
        } else {
            $this->redirect('/payments');
        }
    }

    /**
     * Get payment by ID for AJAX requests
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

                $payment = $this->paymentModel->getPaymentById($id, $institutionId);

                if (!$payment) {
                    header('Content-Type: application/json');
                    http_response_code(404);
                    echo json_encode(['error' => 'Pagamento não encontrado']);
                    exit;
                }

                header('Content-Type: application/json');
                echo json_encode($payment);
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
     * View boleto for a payment
     */
    public function viewBoleto($id)
    {
        if (!isset($_SESSION['user'])) {
            die('Acesso negado');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Get payment and institution data
            $payment = $this->paymentModel->getPaymentForBoleto($id, $institutionId);
            $institution = $this->institutionModel->getInstitutionBankInfo($institutionId);

            if (!$institution) {
                throw new \Exception('Instituição não encontrada');
            }

            if (empty($payment['boleto_code'])) {
                throw new \Exception('Boleto não encontrado');
            }

            if (empty($institution['bank_assignor_name']) || 
                empty($institution['bank_agency']) || 
                empty($institution['bank_account'])) {
                throw new \Exception('Dados bancários da instituição incompletos. Configure-os nas configurações da instituição.');
            }

            // Gerar código de barras
            $barcode_image = $this->generateBarcodeImage($payment['boleto_code']);

            // Retornar view diretamente sem layout
            echo $this->renderView('payments/boleto', [
                'payment' => $payment,
                'institution' => $institution,
                'barcode_image' => $barcode_image
            ]);
            exit;
        } catch (\Exception $e) {
            die('Erro ao gerar boleto: ' . $e->getMessage());
        }
    }

    /**
     * Generate barcode image from code
     * @param string $code The code to generate barcode from
     * @return string Base64 encoded image
     */
    private function generateBarcodeImage($code) {
        try {
            // Create new barcode generator instance
            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
            
            // Generate barcode image
            $barcode = $generator->getBarcode(
                $code,
                $generator::TYPE_CODE_128,
                3, // bar width
                50 // bar height
            );

            // Convert to base64 for embedding in HTML
            return base64_encode($barcode);
        } catch (\Exception $e) {
            error_log('Error generating barcode: ' . $e->getMessage());
            // Return a placeholder in case of error
            return base64_encode('ERROR_GENERATING_BARCODE');
        }
    }
}
