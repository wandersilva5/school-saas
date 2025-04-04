<?php

namespace App\Controllers\Api;

use App\Models\Course;

class CourseController extends ApiBaseController
{
    private $courseModel;

    public function __construct()
    {
        $this->courseModel = new Course();
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
            
            // Get courses with pagination
            $courses = $this->courseModel->getCourses($institutionId, $limit, $offset);
            $totalCourses = $this->courseModel->getTotalCourses($institutionId);
            $totalPages = ceil($totalCourses / $limit);
            
            return $this->successResponse([
                'courses' => $courses,
                'pagination' => [
                    'total' => $totalCourses,
                    'page' => $page,
                    'limit' => $limit,
                    'total_pages' => $totalPages
                ]
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving courses: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        $this->requireAuth();
        
        try {
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Get course details
            $course = $this->courseModel->getCourseById($id, $institutionId);
            
            if (!$course) {
                return $this->errorResponse('Course not found', 404);
            }
            
            // Get subjects for this course
            // This would typically come from a SubjectModel, but we'll simulate it here
            $subjectModel = new \App\Models\Subject();
            $subjects = $subjectModel->getSubjectsByCourse($id, $institutionId);
            
            return $this->successResponse([
                'course' => $course,
                'subjects' => $subjects
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving course: ' . $e->getMessage(), 500);
        }
    }
}