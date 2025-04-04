<?php

namespace App\Controllers\Api;

use App\Models\ClassModel;

class ClassController extends ApiBaseController
{
    private $classModel;

    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->handleCorsOptions();
    }

    public function index()
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Pagination parameters
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
            $offset = ($page - 1) * $limit;
            
            // Get classes with pagination
            $classes = $this->classModel->getClasses($institutionId, $limit, $offset);
            $totalClasses = $this->classModel->getTotalClasses($institutionId);
            $totalPages = ceil($totalClasses / $limit);
            
            return $this->successResponse([
                'classes' => $classes,
                'pagination' => [
                    'total' => $totalClasses,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $totalPages
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving classes: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $this->requireAuth();
        
        try {
            // Get class details
            $class = $this->classModel->getClassById($id);
            
            if (!$class) {
                return $this->errorResponse('Class not found', 404);
            }
            
            return $this->successResponse([
                'class' => $class
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving class: ' . $e->getMessage(), 500);
        }
    }

    public function getStudents($id)
    {
        $this->requireAuth();
        
        try {
            // Check if class exists
            $class = $this->classModel->getClassById($id);
            
            if (!$class) {
                return $this->errorResponse('Class not found', 404);
            }
            
            // Get students in the class
            $students = $this->classModel->getStudentsByClass($id);
            
            return $this->successResponse([
                'class' => $class,
                'students' => $students
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving class students: ' . $e->getMessage(), 500);
        }
    }

    public function getAvailableStudents($id)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Check if class exists
            $class = $this->classModel->getClassById($id);
            
            if (!$class) {
                return $this->errorResponse('Class not found', 404);
            }
            
            // Get available students
            $students = $this->classModel->getAvailableStudents($institutionId, $id);
            
            return $this->successResponse([
                'available_students' => $students
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving available students: ' . $e->getMessage(), 500);
        }
    }
}