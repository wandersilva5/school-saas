<?php

namespace App\Controllers;

use App\Models\Responsavel;

class ResponsavelController extends BaseController
{
    private $responsavelModel;

    public function __construct()
    {
        $this->responsavelModel = new Responsavel();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $institutionId = $_SESSION['user']['institution_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $responsaveis = $this->responsavelModel->getResponsaveis($institutionId, $limit, $offset);
        $totalResponsaveis = $this->responsavelModel->getTotalResponsaveis($institutionId);
        $totalPages = ceil($totalResponsaveis / $limit);

        $this->render('responsaveis/index', [
            'pageTitle' => 'Gerenciar Responsáveis',
            'responsaveis' => $responsaveis,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function store()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'nome' => $_POST['nome'],
                    'email' => $_POST['email'],
                    'telefone' => $_POST['telefone'],
                    'cpf' => $_POST['cpf'],
                    'institution_id' => $_SESSION['user']['institution_id']
                ];

                $this->responsavelModel->create($data);
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Responsável cadastrado com sucesso!'
                ];
                $this->redirect('/responsaveis');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Erro ao cadastrar responsável: ' . $e->getMessage()
                ];
                $this->redirect('/responsaveis');
            }
        }
    }

    public function show($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $responsavel = $this->responsavelModel->getResponsavelById($id);
        $alunos = $this->responsavelModel->getAlunosByResponsavel($id);

        $this->render('responsaveis/show', [
            'pageTitle' => 'Detalhes do Responsável',
            'responsavel' => $responsavel,
            'alunos' => $alunos
        ]);
    }

    public function update()
    {
        if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        try {
            $id = $_POST['id'];
            $data = [
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'telefone' => $_POST['telefone'],
                'cpf' => $_POST['cpf'],
                'active' => isset($_POST['active']) ? 1 : 0
            ];

            $this->responsavelModel->update($id, $data);
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Responsável atualizado com sucesso!'
            ];
            $this->redirect('/responsaveis');
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar responsável: ' . $e->getMessage()
            ];
            $this->redirect('/responsaveis');
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }

        try {
            $id = $_POST['id'];
            $this->responsavelModel->delete($id);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function getById()
    {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            try {
                $id = $_GET['id'];
                $responsavel = $this->responsavelModel->getResponsavelById($id);
                
                header('Content-Type: application/json');
                echo json_encode($responsavel);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }
}