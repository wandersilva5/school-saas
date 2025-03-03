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

        $institutions = $this->roleModel->getInstitutions();
        return $this->render('institution/index', [
            'institutions' => $institutions,
            'currentPage' => 'institution',
            'pageTitle' => 'Gerenciar Instituições'
        ]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        try {
            $name = $_POST['name'];
            $domain = $_POST['domain'];
            $logo_url = $this->uploadImage($_FILES['logo_url']);

            $this->db->beginTransaction();

            // Insere a instituição
            $stmt = $this->db->prepare(
                "INSERT INTO institutions (name, domain, logo_url, created_at) 
                 VALUES (?, ?, ?, NOW())"
            );
            
            $stmt->execute([
                $name,
                $domain,
                $logo_url,
            ]);

            $this->db->commit();
            header('Location: /institutions?success=1');

        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /institutions?error=' . urlencode($e->getMessage()));
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
            $logo_url = isset($_FILES['logo_url']) ? $this->uploadImage($_FILES['logo_url']) : $_POST['existing_logo_url'];

            $this->db->beginTransaction();

            // Atualiza a instituição
            $stmt = $this->db->prepare(
                "UPDATE institutions SET name = ?, domain = ?, logo_url = ?, updated_at = NOW() 
                 WHERE id = ?"
            );
            
            $stmt->execute([
                $name,
                $domain,
                $logo_url,
                $id
            ]);

            $this->db->commit();
            header('Location: /institutions?success=1');

        } catch (\Exception $e) {
            $this->db->rollBack();
            header('Location: /institutions?error=' . urlencode($e->getMessage()));
        }
    }

    private function uploadImage($file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Erro no upload do arquivo.');
        }

        // Verifica se o arquivo é um PNG
        if ($file['type'] !== 'image/png') {
            throw new \Exception('Somente arquivos PNG são permitidos.');
        }

        // Verifica as dimensões da imagem
        list($width, $height) = getimagesize($file['tmp_name']);
        if ($width !== 500 || $height !== 500) {
            // Redimensiona a imagem para 500x500 pixels
            $image = imagecreatefrompng($file['tmp_name']);
            $resizedImage = imagescale($image, 500, 500);
            imagedestroy($image);

            // Salva a imagem redimensionada em um arquivo temporário
            $tempFile = tempnam(sys_get_temp_dir(), 'upload');
            imagepng($resizedImage, $tempFile);
            imagedestroy($resizedImage);

            // Atualiza o caminho do arquivo para o arquivo temporário
            $file['tmp_name'] = $tempFile;
        }

        $uploadDir = __DIR__ . '/../../public/uploads/';
        $fileName = uniqid() . '-' . basename($file['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return '/uploads/' . $fileName;
        } else {
            throw new \Exception('Falha ao mover o arquivo enviado.');
        }
    }
}