<?php

namespace App\Controllers;

use App\Models\SliderImage;

class SliderImageController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new SliderImage();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }
        
        $images = $this->model->getAll($_SESSION['user']['institution_id']);
        return $this->render('slider-images/index', ['images' => $images]);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
            $file = $_FILES['image'];
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $uniqueId = substr(md5(uniqid()), 0, 11);
            $filename = 'slide-' . date('Y-m-d') . '.' . $uniqueId . '.' . $extension;
            $uploadPath = __DIR__ . '/../../public/uploads/slider/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
                $this->model->create([
                    'image_url' => $filename,
                    'institution_id' => $_SESSION['user']['institution_id'],
                    'order_position' => 999
                ]);
                
                echo json_encode(['success' => true]);
                exit;
            }
        }
        
        header('Location: /slider-images');
        exit;
    }

    public function delete($id)
    {
        try {
            // Busca a imagem antes de deletar para obter o nome do arquivo
            $image = $this->model->getById($id, $_SESSION['user']['institution_id']);
            
            if (!$image) {
                echo json_encode(['success' => false, 'message' => 'Imagem não encontrada']);
                exit;
            }

            // Caminho completo do arquivo
            $filePath = __DIR__ . '/../../public/uploads/slider/' . $image['image_url'];

            // Deleta o arquivo físico se ele existir
            if (file_exists($filePath) && !unlink($filePath)) {
                throw new \Exception('Não foi possível excluir o arquivo físico');
            }

            // Deleta o registro do banco de dados
            if (!$this->model->delete($id, $_SESSION['user']['institution_id'])) {
                throw new \Exception('Não foi possível excluir o registro do banco de dados');
            }

            echo json_encode(['success' => true]);
            exit;

        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }

    public function updateOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            foreach ($data['images'] as $position => $id) {
                $this->model->updateOrder($id, $position);
            }
            echo json_encode(['success' => true]);
            exit;
        }
    }
}
