<?php

namespace App\Controllers\Api;

use App\Models\Student;
use App\Models\UserInfo;

class StudentController extends ApiBaseController
{
    private $studentModel;
    private $userInfoModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->userInfoModel = new UserInfo();
        $this->handleCorsOptions();
    }

    public function index()
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Optional pagination parameters
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;
            
            // Get students with pagination
            $students = $this->studentModel->getAllStudents($institutionId);
            
            // Apply pagination manually (since the existing model might not support it)
            $totalStudents = count($students);
            $paginatedStudents = array_slice($students, $offset, $limit);
            
            return $this->successResponse([
                'students' => $paginatedStudents,
                'pagination' => [
                    'total' => $totalStudents,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => ceil($totalStudents / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving students: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $student = $this->studentModel->getStudentById($id, $institutionId);
            
            if (!$student) {
                return $this->errorResponse('Student not found', 404);
            }
            
            // Get additional information
            $studentInfo = $this->userInfoModel->getUserInfoById($id);
            
            // Get guardian information if available
            $guardian = null;
            if (isset($student['guardian_id']) && !empty($student['guardian_id'])) {
                $guardian = $this->studentModel->getGuardianById($student['guardian_id']);
            }
            
            // Return combined data
            return $this->successResponse([
                'student' => $student,
                'student_info' => $studentInfo,
                'guardian' => $guardian
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving student: ' . $e->getMessage(), 500);
        }
    }

    public function getInfo($id)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $studentInfo = $this->userInfoModel->getStudentInfo($id, $institutionId);
            
            if (!$studentInfo) {
                return $this->errorResponse('Student info not found', 404);
            }
            
            return $this->successResponse([
                'student_info' => $studentInfo
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving student info: ' . $e->getMessage(), 500);
        }
    }

    public function getStudentsByGuardian($guardianId)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Check if user has permission (is the guardian or has admin rights)
            if ($_SESSION['user']['id'] != $guardianId && !in_array('TI', $_SESSION['user']['roles'])) {
                return $this->errorResponse('Permission denied', 403);
            }
            
            // For this we need to implement a new method since the existing models don't have this exact functionality
            // This is a simplified implementation
            $students = [];
            
            // Query to get students linked to guardian
            $db = \App\Config\Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT 
                    u.id,
                    u.name,
                    u.email,
                    u.active,
                    i.name as institution_name,
                    ui.registration_number,
                    ui.birth_date
                FROM guardians_students gs
                JOIN users u ON gs.student_user_id = u.id
                LEFT JOIN institutions i ON u.institution_id = i.id
                LEFT JOIN user_info ui ON u.id = ui.user_id
                WHERE gs.guardian_user_id = ?
                AND u.institution_id = ?
                AND u.deleted_at IS NULL
            ");
            
            $stmt->execute([$guardianId, $institutionId]);
            $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return $this->successResponse([
                'students' => $students
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving students: ' . $e->getMessage(), 500);
        }
    }
}