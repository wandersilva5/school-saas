<?php

namespace App\Controllers;

use App\Config\Database;

class StudentController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $institutionId = $_SESSION['user']['institution_id'];
        
        // Busca todos os responsáveis ativos
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? 
            AND r.name = 'Responsavel'
            AND u.active = 1
            ORDER BY u.name
        ");
        $stmt->execute([$institutionId]);
        $guardians = $stmt->fetchAll();

        // Busca todos os alunos
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.name as student_name,
                u.email as student_email,
                u.phone,
                u.active,
                gu.name as guardian_name,
                gu.id as guardian_id
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            LEFT JOIN guardians_students gs ON u.id = gs.student_user_id
            LEFT JOIN users gu ON gs.guardian_user_id = gu.id
            WHERE u.institution_id = ? AND r.name = 'Aluno'
            ORDER BY u.name
        ");
        $stmt->execute([$institutionId]);
        $students = $stmt->fetchAll();

        $this->render('students/index', [
            'students' => $students,
            'guardians' => $guardians
        ]);
    }

    public function create()
    {
        $institutionId = $_SESSION['user']['institution_id'];
        $stmt = $this->db->prepare("
            SELECT u.id, u.name 
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? AND r.name = 'Responsavel'
        ");
        $stmt->execute([$institutionId]);
        $guardians = $stmt->fetchAll();

        $stmt = $this->db->prepare("
            SELECT u.id, u.name 
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? AND r.name = 'Aluno'
            AND u.id NOT IN (SELECT student_user_id FROM guardians_students)
        ");
        $stmt->execute([$institutionId]);
        $available_students = $stmt->fetchAll();

        $this->render('students/create', [
            'guardians' => $guardians,
            'available_students' => $available_students
        ]);
    }

    public function store()
    {
        try {
            $this->db->beginTransaction();

            $institutionId = $_SESSION['user']['institution_id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $phone = $_POST['phone'];
            $guardianId = $_POST['guardian_id'];

            // Primeiro, cria o usuário
            $stmt = $this->db->prepare("
                INSERT INTO users (name, email, password, phone, institution_id, active, created_at) 
                VALUES (?, ?, ?, ?, ?, 1, NOW())
            ");
            $stmt->execute([$name, $email, $password, $phone, $institutionId]);
            $studentUserId = $this->db->lastInsertId();

            // Atribui o papel de Aluno
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id)
                SELECT ?, id FROM roles WHERE name = 'Aluno'
            ");
            $stmt->execute([$studentUserId]);

            // Cria o relacionamento com o responsável
            $stmt = $this->db->prepare("
                INSERT INTO guardians_students (student_user_id, guardian_user_id, institution_id) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$studentUserId, $guardianId, $institutionId]);

            $this->db->commit();
            $this->redirect('/students?success=1');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            $this->redirect('/students?error=' . urlencode('Erro ao criar aluno'));
        }
    }

    public function show($id) 
    {
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.phone,
                    u.active,
                    gs.guardian_user_id
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                LEFT JOIN guardians_students gs ON u.id = gs.student_user_id
                WHERE u.id = ? 
                AND u.institution_id = ?
                AND r.name = 'Aluno'
            ");
            $stmt->execute([$id, $institutionId]);
            $student = $stmt->fetch();

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

    public function edit($id)
    {
        $institutionId = $_SESSION['user']['institution_id'];
        
        $stmt = $this->db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $student = $stmt->fetch();

        $stmt = $this->db->prepare("
            SELECT u.id, u.name 
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? AND r.name = 'Responsavel'
        ");
        $stmt->execute([$institutionId]);
        $guardians = $stmt->fetchAll();

        $stmt = $this->db->prepare("
            SELECT u.id, u.name 
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? AND r.name = 'Aluno'
            AND u.id NOT IN (SELECT student_user_id FROM guardians_students)
        ");
        $stmt->execute([$institutionId]);
        $available_students = $stmt->fetchAll();

        $this->render('students/edit', [
            'student' => $student, 
            'guardians' => $guardians,
            'available_students' => $available_students
        ]);
    }

    public function update($id)
    {
        try {
            $this->db->beginTransaction();

            $institutionId = $_SESSION['user']['institution_id'];
            
            // Atualiza os dados do usuário
            $stmt = $this->db->prepare("
                UPDATE users 
                SET 
                    name = ?, 
                    email = ?, 
                    phone = ?,
                    active = ?
                WHERE id = ? 
                AND institution_id = ?
            ");
            
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                isset($_POST['active']) ? 1 : 0,
                $id,
                $institutionId
            ]);

            // Atualiza ou insere o relacionamento com o responsável
            $stmt = $this->db->prepare("
                INSERT INTO guardians_students 
                    (student_user_id, guardian_user_id, institution_id) 
                VALUES 
                    (?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    guardian_user_id = VALUES(guardian_user_id)
            ");
            
            $stmt->execute([
                $id,
                $_POST['guardian_id'],
                $institutionId
            ]);

            $this->db->commit();
            $this->redirect('/students?success=1');

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            $this->redirect('/students?error=' . urlencode('Erro ao atualizar aluno'));
        }
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM guardians_students WHERE student_user_id = ?");
        $stmt->execute([$id]);

        $this->redirect('/students');
    }
}
