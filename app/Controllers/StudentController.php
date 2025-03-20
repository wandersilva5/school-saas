<?php

namespace App\Controllers;

use App\Models\Student;
use App\Models\UserInfo;
use PDO;

class StudentController extends BaseController
{
    private $studentModel;
    private $userInfoModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->userInfoModel = new UserInfo();
    }

    public function index()
    {
        $institutionId = $_SESSION['user']['institution_id'];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;

        $students = $this->studentModel->getAllStudents($institutionId);
        $guardians = $this->studentModel->getAllGuardians($institutionId);
        $user_info = $this->userInfoModel->getAlunoInfoById($institutionId);


        $this->render('students/index', [
            'students' => $students,
            'guardians' => $guardians,
            'user_info' => $user_info,
            'user' => $_SESSION['user'],
            'currentPage' => $page,
            'currentRoute' => 'students',
            'title' => 'Listagem de Alunos'
        ]);
    }

    public function show($id)
    {
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $student = $this->studentModel->getStudentById($id, $institutionId);

            if (!$student) {
                throw new \Exception('Aluno não encontrado');
            }

            header('Content-Type: application/json');
            echo json_encode($student);
            exit;
        } catch (\Exception $e) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public function store()
    {
        try {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'phone' => $_POST['phone'],
                'guardian_id' => $_POST['guardian_id'],
                'institution_id' => $_SESSION['user']['institution_id']
            ];

            $this->studentModel->createStudent($data);
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Aluno criado com sucesso!'
            ];
            $this->redirect('/students');
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao criar aluno: ' . $e->getMessage()
            ];
            $this->redirect('/students');
        }
    }

    public function update($id)
    {
        try {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'active' => isset($_POST['active']) ? 1 : 0,
                'guardian_id' => $_POST['guardian_id'],
                'institution_id' => $_SESSION['user']['institution_id']
            ];

            $this->studentModel->updateStudent($id, $data);
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Aluno atualizado com sucesso!'
            ];
            $this->redirect('/students');
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao atualizar aluno: ' . $e->getMessage()
            ];
            $this->redirect('/students');
        }
    }

    public function delete($id)
    {
        try {
            $this->studentModel->deleteStudent($id);
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Aluno excluído com sucesso!'
            ];
            $this->redirect('/students');
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao excluir aluno: ' . $e->getMessage()
            ];
            $this->redirect('/students');
        }
    }

    public function getInfo($id) {
        try {
            $userInfoModel = new \App\Models\UserInfo();
            $student = $userInfoModel->getStudentInfo($id, $_SESSION['user']['institution_id']);
            
            if (!$student) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'Aluno não encontrado']);
                exit;
            }
            
            header('Content-Type: application/json');
            echo json_encode($student);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}
