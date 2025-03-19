<?php

namespace App\Controllers;

use App\Models\Institution;
use \Exception;
use \PDO;

class InstitutionController extends BaseController
{
    private $db;
    private Institution $roleModel;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
        $this->roleModel = new Institution();
    }

    public function index()
    {
        // Verifica se o usuário tem permissão
        if (!in_array('TI', $_SESSION['user']['roles'] ?? [])) {
            header('Location: /dashboard');
            exit;
        }

        // Configuração da paginação
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 5; // itens por página
            $offset = ($page - 1) * $limit;

            // Conta total de registros
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM institutions 
                WHERE deleted_at IS NULL
            ");
            $stmt->execute();
            $totalInstitutions = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalInstitutions / $limit);

            // Busca instituições com paginação
            $stmt = $this->db->prepare("
                SELECT *
                FROM institutions 
                WHERE deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");

            // Corrige a ordem dos parâmetros para corresponder à query
            $stmt->execute([$limit, $offset]);
            $institutions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $this->render('institution/index', [
                'institutions' => $institutions,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'pageTitle' => 'Gerenciar Instituições'
            ]);
        } catch (\PDOException $e) {
            error_log("Erro na paginação de instituições: " . $e->getMessage());
            header('Location: /institution?error=' . urlencode('Erro ao carregar as instituições'));
            exit;
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
}
