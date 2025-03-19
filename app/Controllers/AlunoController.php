<?php

namespace App\Controllers;

use App\Models\Aluno;

class AlunoController extends BaseController
{
    private $alunoModel;

    public function __construct()
    {
        $this->alunoModel = new Aluno();
    }

    public function index()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $institutionId = $_SESSION['user']['institution_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $alunos = $this->alunoModel->getAlunos($institutionId, $limit, $offset);
        $totalAlunos = $this->alunoModel->getTotalAlunos($institutionId);
        $totalPages = ceil($totalAlunos / $limit);
        $responsaveis = $this->alunoModel->getResponsaveis();

        $this->render('alunos/index', [
            'pageTitle' => 'Gerenciar Alunos',
            'alunos' => $alunos,
            'responsaveis' => $responsaveis,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'currentSection' => 'alunos' // Adicionado para marcar o item ativo no sidebar
        ]);
    }

    public function store()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'nome' => $_POST['nome'],
                    'data_nascimento' => $_POST['data_nascimento'],
                    'matricula' => $_POST['matricula'],
                    'responsavel_id' => $_POST['responsavel_id'],
                    'institution_id' => $_SESSION['user']['institution_id']
                ];

                $this->alunoModel->create($data);
                $this->redirect('/alunos?success=1');
            } catch (\Exception $e) {
                $this->redirect('/alunos?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function show($id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $aluno = $this->alunoModel->getAlunoById($id);

        $this->render('alunos/show', [
            'pageTitle' => 'Detalhes do Aluno',
            'aluno' => $aluno,
            'currentSection' => 'alunos' // Adicionado para marcar o item ativo no sidebar
        ]);
    }

    public function update()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $data = [
                    'nome' => $_POST['nome'],
                    'data_nascimento' => $_POST['data_nascimento'],
                    'matricula' => $_POST['matricula'],
                    'responsavel_id' => $_POST['responsavel_id'],
                    'active' => isset($_POST['active']) ? 1 : 0
                ];

                $this->alunoModel->update($id, $data);
                $this->redirect('/alunos?success=1');
            } catch (\Exception $e) {
                $this->redirect('/alunos?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function delete()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id = $_POST['id'];
                $this->alunoModel->delete($id);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function getById()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            try {
                $id = $_GET['id'];
                $aluno = $this->alunoModel->getAlunoById($id);
                
                header('Content-Type: application/json');
                echo json_encode($aluno);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }
}
