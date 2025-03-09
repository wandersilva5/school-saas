<?php

namespace App\Controllers;

use App\Models\CarouselSlide;

class CarouselSlideController extends BaseController
{
    private $carouselSlideModel;

    public function __construct()
    {
        $this->carouselSlideModel = new CarouselSlide();
    }

    public function index()
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'Comunicação')) {
            $_SESSION['error'] = "Acesso não autorizado. Somente usuários de comunicação podem gerenciar o carrossel.";
            header('Location: /dashboard-institution');
            exit;
        }

        // Obter slides da mesma instituição
        $slides = $this->carouselSlideModel->getByInstitutionId($user['institution_id']);

        return $this->render('carousel/index', [
            'user' => $user,
            'pageTitle' => 'Gerenciar Carrossel',
            'currentPage' => 'carousel',
            'slides' => $slides
        ]);
    }

    public function create()
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'communication')) {
            $_SESSION['error'] = "Acesso não autorizado. Somente usuários de comunicação podem gerenciar o carrossel.";
            header('Location: /dashboard-institution');
            exit;
        }

        return $this->render('carousel/create', [
            'user' => $user,
            'pageTitle' => 'Adicionar Slide',
            'currentPage' => 'carousel'
        ]);
    }

    public function store()
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'communication')) {
            $_SESSION['error'] = "Acesso não autorizado. Somente usuários de comunicação podem gerenciar o carrossel.";
            header('Location: /dashboard-institution');
            exit;
        }

        // Verificar se é um POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carousel');
            exit;
        }

        // Processar upload da imagem
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/carousel/';
        
        // Criar diretório se não existir
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageUrl = '';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            // Mover arquivo para o diretório final
            if (move_uploaded_file($tmpName, $uploadFile)) {
                $imageUrl = '/uploads/carousel/' . $fileName;
            } else {
                $_SESSION['error'] = "Falha ao fazer upload da imagem.";
                header('Location: /carousel/create');
                exit;
            }
        } else {
            $_SESSION['error'] = "Imagem inválida ou não selecionada.";
            header('Location: /carousel/create');
            exit;
        }

        // Dados do slide
        $slideData = [
            'image_url' => $imageUrl,
            'institution_id' => $user['institution_id']
        ];

        // Salvar no banco
        try {
            $this->carouselSlideModel->create($slideData);
            $_SESSION['success'] = "Slide adicionado com sucesso!";
            header('Location: /carousel');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erro ao adicionar slide: " . $e->getMessage();
            header('Location: /carousel/create');
            exit;
        }
    }

    public function edit($id)
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'communication')) {
            $_SESSION['error'] = "Acesso não autorizado. Somente usuários de comunicação podem gerenciar o carrossel.";
            header('Location: /dashboard-institution');
            exit;
        }

        // Obter slide
        $slide = $this->carouselSlideModel->getById($id);
        
        // Verificar se o slide existe e pertence à mesma instituição
        if (!$slide || $slide['institution_id'] != $user['institution_id']) {
            $_SESSION['error'] = "Slide não encontrado ou acesso não autorizado.";
            header('Location: /carousel');
            exit;
        }

        return $this->render('carousel/edit', [
            'user' => $user,
            'pageTitle' => 'Editar Slide',
            'currentPage' => 'carousel',
            'slide' => $slide
        ]);
    }

    public function update($id)
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'communication')) {
            $_SESSION['error'] = "Acesso não autorizado. Somente usuários de comunicação podem gerenciar o carrossel.";
            header('Location: /dashboard-institution');
            exit;
        }

        // Verificar se é um POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /carousel');
            exit;
        }

        // Obter slide
        $slide = $this->carouselSlideModel->getById($id);
        
        // Verificar se o slide existe e pertence à mesma instituição
        if (!$slide || $slide['institution_id'] != $user['institution_id']) {
            $_SESSION['error'] = "Slide não encontrado ou acesso não autorizado.";
            header('Location: /carousel');
            exit;
        }

        $imageUrl = $slide['image_url'];
        
        // Processar upload da imagem, se fornecida
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/carousel/';
            
            // Criar diretório se não existir
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $tmpName = $_FILES['image']['tmp_name'];
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            // Mover arquivo para o diretório final
            if (move_uploaded_file($tmpName, $uploadFile)) {
                // Remover imagem anterior se não for uma URL externa
                if ($slide['image_url'] && !filter_var($slide['image_url'], FILTER_VALIDATE_URL)) {
                    $oldFile = $_SERVER['DOCUMENT_ROOT'] . $slide['image_url'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                $imageUrl = '/uploads/carousel/' . $fileName;
            } else {
                $_SESSION['error'] = "Falha ao fazer upload da imagem.";
                header('Location: /carousel/edit/' . $id);
                exit;
            }
        }

        // Dados do slide
        $slideData = [
            'image_url' => $imageUrl,
            'institution_id' => $user['institution_id'],
            'order_num' => $slide['order_num']
        ];

        // Atualizar no banco
        try {
            $this->carouselSlideModel->update($id, $slideData);
            $_SESSION['success'] = "Slide atualizado com sucesso!";
            header('Location: /carousel');
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erro ao atualizar slide: " . $e->getMessage();
            header('Location: /carousel/edit/' . $id);
            exit;
        }
    }

    public function delete($id)
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'communication')) {
            $_SESSION['error'] = "Acesso não autorizado. Somente usuários de comunicação podem gerenciar o carrossel.";
            header('Location: /dashboard-institution');
            exit;
        }

        // Obter slide
        $slide = $this->carouselSlideModel->getById($id);
        
        // Verificar se o slide existe e pertence à mesma instituição
        if (!$slide || $slide['institution_id'] != $user['institution_id']) {
            $_SESSION['error'] = "Slide não encontrado ou acesso não autorizado.";
            header('Location: /carousel');
            exit;
        }

        // Remover imagem se não for uma URL externa
        if ($slide['image_url'] && !filter_var($slide['image_url'], FILTER_VALIDATE_URL)) {
            $oldFile = $_SERVER['DOCUMENT_ROOT'] . $slide['image_url'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Excluir do banco
        try {
            $this->carouselSlideModel->delete($id, $user['institution_id']);
            $_SESSION['success'] = "Slide excluído com sucesso!";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Erro ao excluir slide: " . $e->getMessage();
        }

        header('Location: /carousel');
        exit;
    }

    public function reorder()
    {
        // Verificar autenticação
        if (!isset($_SESSION['user'])) {
            $response = ['success' => false, 'message' => 'Usuário não autenticado'];
            echo json_encode($response);
            exit;
        }

        $user = $_SESSION['user'];
        
        // Verificar se o usuário tem o papel de comunicação
        if (!$this->hasRole($user, 'communication')) {
            $response = ['success' => false, 'message' => 'Acesso não autorizado'];
            echo json_encode($response);
            exit;
        }

        // Verificar se é um POST e se foi enviado um JSON
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['slideIds'])) {
            $response = ['success' => false, 'message' => 'Dados inválidos'];
            echo json_encode($response);
            exit;
        }

        $slideIds = json_decode($_POST['slideIds'], true);
        
        if (!is_array($slideIds)) {
            $response = ['success' => false, 'message' => 'Formato de dados inválido'];
            echo json_encode($response);
            exit;
        }

        // Reordenar slides
        try {
            $this->carouselSlideModel->reorder($slideIds, $user['institution_id']);
            $response = ['success' => true, 'message' => 'Slides reordenados com sucesso'];
        } catch (\Exception $e) {
            $response = ['success' => false, 'message' => 'Erro ao reordenar slides: ' . $e->getMessage()];
        }

        echo json_encode($response);
        exit;
    }

    private function hasRole($user, $roleName)
    {
        if (!isset($user['roles']) || !is_array($user['roles'])) {
            return false;
        }
        
        foreach ($user['roles'] as $role) {
            if (is_array($role) && isset($role['name']) && $role['name'] === $roleName) {
                return true;
            } elseif (is_string($role) && $role === $roleName) {
                return true;
            }
        }
        
        return false;
    }
}
