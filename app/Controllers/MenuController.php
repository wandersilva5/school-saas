<?php
namespace App\Controllers;

use App\Models\Menu;

class MenuController extends BaseController {
    private $menuModel;

    public function __construct() {
        $this->menuModel = new Menu();
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();
        
        $menus = $this->menuModel->getAll();
        $roles = $this->menuModel->getAllRoles();
        $headers = $this->menuModel->getAllHeaders();
        
        return $this->render('menus/index', [
            'menus' => $menus,
            'roles' => $roles,
            'headers' => $headers,
            'pageTitle' => "Gerenciar Menus",
            'currentPage' => 'menus',
        ]);
    }

    public function store() {
        $data = [
            'name' => $_POST['name'],
            'url' => $_POST['url'],
            'icon' => $_POST['icon'],
            'header' => $_POST['header'],
            'route' => $_POST['route'],
            'required_roles' => $_POST['required_roles'],
            'order_index' => (int)$_POST['order_index']
        ];

        if ($this->menuModel->create($data)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function update($id) {
        $data = [
            'name' => $_POST['name'],
            'url' => $_POST['url'],
            'icon' => $_POST['icon'],
            'header' => $_POST['header'],
            'route' => $_POST['route'],
            'required_roles' => $_POST['required_roles'],
            'order_index' => (int)$_POST['order_index']
        ];

        if ($this->menuModel->update($id, $data)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function delete($id) {
        if ($this->menuModel->delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    public function reorder() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $items = array_map(function($item) use ($data) {
                return [
                    'id' => $item['id'],
                    'order_index' => $item['order_index'],
                    'header' => $data['header']
                ];
            }, $data['items']);

            if ($this->menuModel->batchUpdate($items)) {
                echo json_encode(['success' => true]);
            } else {
                throw new \Exception('Erro ao atualizar ordem');
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
