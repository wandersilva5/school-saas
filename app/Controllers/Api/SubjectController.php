<?php

namespace App\Controllers\Api;

use App\Models\Subject;

class SubjectController extends ApiBaseController
{
    private $subjectModel;

    public function __construct()
    {
        $this->subjectModel = new Subject();
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
            
            // Get subjects with pagination
            $subjects = $this->subjectModel->getSubjects($institutionId, $limit, $offset);
            $totalSubjects = $this->subjectModel->getTotalSubjects($institutionId);
            $totalPages = ceil($totalSubjects / $limit);
            
            return $this->successResponse([
                'subjects' => $subjects,
                'pagination' => [
                    'total' => $totalSubjects,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $totalPages
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving subjects: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Get subject details
            $subject = $this->subjectModel->getSubjectById($id, $institutionId);
            
            if (!$subject) {
                return $this->errorResponse('Subject not found', 404);
            }
            
            return $this->successResponse([
                'subject' => $subject
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving subject: ' . $e->getMessage(), 500);
        }
    }

    public function getSubjectsByCourse($courseId)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Get subjects for the course
            $subjects = $this->subjectModel->getSubjectsByCourse($courseId, $institutionId);
            
            return $this->successResponse([
                'subjects' => $subjects
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving subjects: ' . $e->getMessage(), 500);
        }
    }
}