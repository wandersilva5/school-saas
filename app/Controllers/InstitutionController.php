<?php

namespace App\Controllers;

use App\Models\Institution;
use \Exception;
use \PDO;

class InstitutionController extends BaseController
{
    private $db;
    private Institution $roleModel;
    private $institutionModel;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
        $this->roleModel = new Institution();
        $this->institutionModel = new Institution();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }
        
        try {
            $responsavelId = $_SESSION['user']['id'];

            // Check if institution_id is set
            if (!isset($_SESSION['user']['institution_id']) || empty($_SESSION['user']['institution_id'])) {
                // No institution selected, check how many they have access to
                $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT i.id) as count
                FROM institutions i
                JOIN guardians_students gs ON gs.institution_id = i.id
                WHERE gs.guardian_user_id = ?
            ");
                $stmt->execute([$responsavelId]);
                $count = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

                if ($count > 1) {
                    // Multiple institutions - render selection screen
                    $stmt = $this->db->prepare("
                    SELECT DISTINCT i.id, i.name, i.logo_url
                    FROM institutions i
                    JOIN guardians_students gs ON gs.institution_id = i.id
                    WHERE gs.guardian_user_id = ?
                    ORDER BY i.name
                ");
                    $stmt->execute([$responsavelId]);
                    $institutions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    return $this->render('home-institution/index', [
                        'pageTitle' => 'Selecione uma Instituição',
                        'institutions' => $institutions
                    ]);
                } elseif ($count == 1) {
                    // Just one institution - set it automatically
                    $stmt = $this->db->prepare("
                    SELECT DISTINCT i.id
                    FROM institutions i
                    JOIN guardians_students gs ON gs.institution_id = i.id
                    WHERE gs.guardian_user_id = ?
                    LIMIT 1
                ");
                    $stmt->execute([$responsavelId]);
                    $institutionId = $stmt->fetch(\PDO::FETCH_ASSOC)['id'];
                    $_SESSION['user']['institution_id'] = $institutionId;
                } else {
                    // No institutions at all
                    return $this->render('home-institution/index', [
                        'pageTitle' => 'Home institution',
                        'error' => 'Nenhuma instituição encontrada. Entre em contato com o suporte.',
                        'alunos' => [],
                        'financeiro' => [],
                        'comunicados' => [],
                        'eventos' => [],
                        'sliderImages' => []
                    ]);
                }
            }

            // Continue with the original code...
            $institutionId = $_SESSION['user']['institution_id'];
            // Rest of your dashboard rendering code...
        } catch (\Exception $e) {
            error_log('Erro: ' . $e->getMessage());
            // Handle error...
        }
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $transactionStarted = false;

        try {
            $name = $_POST['name'];
            $domain = $_POST['domain'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $nameContact = $_POST['name_contact'];
            $logo_url = $this->uploadImage($_FILES['logo_url']);

            $this->db->beginTransaction();
            $transactionStarted = true;

            // Insere a instituição
            $stmt = $this->db->prepare(
                "INSERT INTO institutions (name, domain, logo_url, email, phone, name_contact, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );

            $stmt->execute([
                $name,
                $domain,
                $logo_url,
                $email,
                $phone,
                $nameContact
            ]);

            $this->db->commit();
            header('Location: /institution?success=1');
            exit;
        } catch (\Exception $e) {
            if ($transactionStarted) {
                $this->db->rollBack();
            }
            header('Location: /institution?error=' . urlencode($e->getMessage()));
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
            $name = $_POST['name'];
            $domain = $_POST['domain'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $nameContact = $_POST['name_contact'];
            $logo_url = isset($_FILES['logo_url']) ? $this->uploadImage($_FILES['logo_url']) : $_POST['existing_logo_url'];

            $this->db->beginTransaction();

            // Atualiza a instituição
            $stmt = $this->db->prepare(
                "UPDATE institutions 
             SET name = ?, domain = ?, logo_url = ?, email = ?, phone = ?, name_contact = ?, updated_at = NOW() 
             WHERE id = ?"
            );

            $stmt->execute([
                $name,
                $domain,
                $logo_url,
                $email,
                $phone,
                $nameContact,
                $id
            ]);

            $this->db->commit();
            header('Location: /institution?success=1');
            exit;
        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /institution?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    private function uploadImage($file)
    {
        // Lógica Upload da imagem aqui
        if (isset($_FILES['logo_url']) && $_FILES['logo_url']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/institutions/';

            // Crie um diretório se ele não existir
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['logo_url']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['logo_url']['tmp_name'], $targetPath)) {
                // O arquivo foi carregado com sucesso, agora salve os dados da instituição
                $logoUrl = '/uploads/institutions/' . $fileName;

                return $logoUrl;
            } else {
                throw new Exception('Falha ao mover o arquivo carregado');
            }
        } else {
            throw new Exception('Nenhum arquivo carregado ou ocorreu um erro de carregamento');
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
            // Get institutions where user has children
            $stmt = $this->db->prepare("
                SELECT DISTINCT i.id, i.name, i.logo_url
                FROM institutions i
                JOIN guardians_students gs ON gs.institution_id = i.id
                WHERE gs.guardian_user_id = ?
                AND i.deleted_at IS NULL
                ORDER BY i.name
            ");
            $stmt->execute([$userId]);
            $institutions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // // If only one institution, select it automatically
            // if (count($institutions) > 1) {
            //     $_SESSION['user']['institution_id'] = $institutions[0]['id'];
            //     header('Location: /dashboard-responsavel');
            //     exit;
            // }

            // Otherwise render the selection page
            return $this->render('institution/list', [
                'institutions' => $institutions,
                'pageTitle' => 'Selecione uma Instituição'
            ]);
        } catch (\Exception $e) {
            error_log("Error in listForResponsavel: " . $e->getMessage());
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
        // Verify user is Responsavel
        if (!isset($_SESSION['user']) || !in_array('Responsavel', $_SESSION['user']['roles'])) {
            header('Location: /login');
            exit;
        }
        // Verify institution exists and user has access
        $stmt = $this->db->prepare("
            SELECT i.id
            FROM institutions i
            JOIN guardians_students gs ON gs.institution_id = i.id
            WHERE i.id = ?
            AND gs.guardian_user_id = ?
            AND i.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$id, $_SESSION['user']['id']]);
        $institution = $stmt->fetch();

        if (!$institution) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Instituição não encontrada ou acesso negado'
            ];
            header('Location: /institutions/list');
            exit;
        }

        // Set selected institution in session
        $_SESSION['user']['institution_id'] = $id;

        // Redirect to dashboard
        header('Location: /dashboard-institution');
        exit;
    }
}
