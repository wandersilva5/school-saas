<?php

namespace App\Controllers;

use App\Models\ClassModel;
use App\Models\Student;

class ClassController extends BaseController
{
    private $classModel;
    private $Student;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->Student = new Student();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        $institutionId = $_SESSION['user']['institution_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $classes = $this->classModel->getClasses($institutionId, $limit, $offset);
        $totalClasses = $this->classModel->getTotalClasses($institutionId);
        $totalPages = ceil($totalClasses / $limit);

        $this->render('classes/index', [
            'pageTitle' => 'Gerenciar Turmas',
            'classes' => $classes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'currentSection' => 'classes' // Para marcar item ativo no sidebar
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
                    'name' => $_POST['name'],
                    'shift' => $_POST['shift'],
                    'year' => $_POST['year'],
                    'capacity' => $_POST['capacity'],
                    'institution_id' => $_SESSION['user']['institution_id']
                ];

                $this->classModel->create($data);
                $this->redirect('/classes?success=1');
            } catch (\Exception $e) {
                $this->redirect('/classes?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function show($id)
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $class = $this->classModel->getClassById($id);
        $students = $this->classModel->getStudentsByClass($id);

        $this->render('classes/show', [
            'pageTitle' => 'Detalhes da Turma',
            'class' => $class,
            'students' => $students,
            'currentSection' => 'classes' // Para marcar item ativo no sidebar
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
                    'name' => $_POST['name'],
                    'shift' => $_POST['shift'],
                    'year' => $_POST['year'],
                    'capacity' => $_POST['capacity'],
                    'active' => isset($_POST['active']) ? 1 : 0
                ];

                $this->classModel->update($id, $data);
                $this->redirect('/classes?success=1');
            } catch (\Exception $e) {
                $this->redirect('/classes?error=' . urlencode($e->getMessage()));
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
                $this->classModel->delete($id);
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
                $class = $this->classModel->getClassById($id);
                
                if (!$class) {
                    http_response_code(404);
                    echo json_encode(['error' => 'Turma não encontrada']);
                    exit;
                }
                
                header('Content-Type: application/json');
                echo json_encode($class);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }
    
    public function getAvailableStudents()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['class_id'])) {
            try {
                $classId = $_GET['class_id'];
                $institutionId = $_SESSION['user']['institution_id'];
                
                $students = $this->classModel->getAvailableStudents($institutionId, $classId);
                
                header('Content-Type: application/json');
                echo json_encode($students);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }
    
    public function updateStatus()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $classId = $_POST['class_id'];
                $studentId = $_POST['student_id'];
                $status = $_POST['status'];
                
                // Carregar o modelo ClassStudent para gerenciar esta operação
                $classStudentModel = new \App\Models\ClassStudent();
                $classStudentModel->updateStatus($classId, $studentId, $status);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }

    public function addStudent()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $classId = $_POST['class_id'];
                $studentId = $_POST['student_id'];
                
                $this->classModel->addStudentToClass($classId, $studentId);
                $this->redirect('/classes/show/' . $classId . '?success=1');
            } catch (\Exception $e) {
                $this->redirect('/classes/show/' . $classId . '?error=' . urlencode($e->getMessage()));
            }
        }
    }

    public function removeStudent()
    {
        // Verificar se o usuário está logado
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $classId = $_POST['class_id'];
                $studentId = $_POST['student_id'];
                
                $this->classModel->removeStudentFromClass($classId, $studentId);
                echo json_encode(['success' => true]);
            } catch (\Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }
    }
}