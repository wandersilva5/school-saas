<?php

namespace App\Controllers;

use App\Models\Subject;
use App\Models\Course;

class SubjectController extends BaseController
{
    private $subjectModel;
    private $courseModel;

    public function __construct()
    {
        $this->subjectModel = new Subject();
        $this->courseModel = new Course();
    }

    /**
     * Display the list of subjects
     */
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alert: User not in session");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();

        $institutionId = $_SESSION['user']['institution_id'];

        // Pagination setup
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10; // items per page
        $offset = ($page - 1) * $limit;

        try {
            $subjects = $this->subjectModel->getSubjects($institutionId, $limit, $offset);
            $totalSubjects = $this->subjectModel->getTotalSubjects($institutionId);
            $totalPages = ceil($totalSubjects / $limit);

            $courses = $this->courseModel->getCourses($institutionId, 100, 0); // Get all courses for dropdown

            $this->render('subjects/index', [
                'pageTitle' => 'Listagem das Disciplinas',
                'subjects' => $subjects,
                'courses' => $courses,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'currentSection' => 'subjects'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Error loading subjects: ' . $e->getMessage()
            ];

            $this->render('subjects/index', [
                'pageTitle' => 'Subject Management',
                'subjects' => [],
                'courses' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'currentSection' => 'subjects'
            ]);
        }
    }

    /**
     * Display a single subject
     */
    public function show($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $subject = $this->subjectModel->getSubjectById($id, $institutionId);

            if (!$subject) {
                throw new \Exception('Subject not found');
            }

            $this->render('subjects/show', [
                'pageTitle' => 'Detalhes da Disciplina',
                'subject' => $subject,
                'currentSection' => 'subjects'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/subjects');
        }
    }

    /**
     * Create form for new subject with course_id
     */
    public function create()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        $institutionId = $_SESSION['user']['institution_id'];
        $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;
        
        try {
            $course = null;
            if ($courseId) {
                $course = $this->courseModel->getCourseById($courseId, $institutionId);
                if (!$course) {
                    throw new \Exception('Course not found');
                }
            }
            
            $courses = $this->courseModel->getCourses($institutionId, 100, 0);
            
            $this->render('subjects/create', [
                'pageTitle' => 'Nova Disciplina',
                'course' => $course,
                'courses' => $courses,
                'currentSection' => 'subjects'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/courses');
        }
    }

    /**
     * Store a new subject
     */
    public function store()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Form data validation
                $name = trim($_POST['name'] ?? '');
                $code = trim($_POST['code'] ?? '');
                $courseId = (int)($_POST['course_id'] ?? 0);

                if (empty($name)) {
                    throw new \Exception('Subject name is required');
                }

                if (empty($code)) {
                    throw new \Exception('Subject code is required');
                }

                if (empty($courseId)) {
                    throw new \Exception('Course is required');
                }

                // Check if course exists and belongs to institution
                $course = $this->courseModel->getCourseById($courseId, $institutionId);
                if (!$course) {
                    throw new \Exception('Course not found or does not belong to your institution');
                }

                // Check if code already exists for this course
                if ($this->subjectModel->codeExists($code, $courseId, $institutionId)) {
                    throw new \Exception('Subject code already exists for this course');
                }

                $data = [
                    'name' => $name,
                    'code' => $code,
                    'course_id' => $courseId,
                    'workload' => !empty($_POST['workload']) ? (int)$_POST['workload'] : null,
                    'semester' => !empty($_POST['semester']) ? (int)$_POST['semester'] : null,
                    'description' => $_POST['description'] ?? null,
                    'user_id' => $_SESSION['user']['id'],
                    'institution_id' => $institutionId,
                    'active' => isset($_POST['active']) ? 1 : 0
                ];

                $this->subjectModel->create($data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Subject created successfully!'
                ];
                
                // Redirect back to course detail if coming from there
                if (isset($_POST['return_to_course']) && $_POST['return_to_course']) {
                    $this->redirect('/courses/show/' . $courseId);
                } else {
                    $this->redirect('/subjects');
                }
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Error creating subject: ' . $e->getMessage()
                ];
                $this->redirect('/subjects');
            }
        } else {
            $this->redirect('/subjects');
        }
    }

    /**
     * Display the form to edit a subject
     */
    public function edit($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $subject = $this->subjectModel->getSubjectById($id, $institutionId);

            if (!$subject) {
                throw new \Exception('Subject not found');
            }

            $courses = $this->courseModel->getCourses($institutionId, 100, 0);

            $this->render('subjects/edit', [
                'pageTitle' => 'Edit Subject',
                'subject' => $subject,
                'courses' => $courses,
                'currentSection' => 'subjects'
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/subjects');
        }
    }

    /**
     * Update an existing subject
     */
    public function update($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if subject exists
                $subject = $this->subjectModel->getSubjectById($id, $institutionId);
                if (!$subject) {
                    throw new \Exception('Subject not found');
                }

                // Form data validation
                $name = trim($_POST['name'] ?? '');
                $code = trim($_POST['code'] ?? '');
                $courseId = (int)($_POST['course_id'] ?? 0);

                if (empty($name)) {
                    throw new \Exception('Subject name is required');
                }

                if (empty($code)) {
                    throw new \Exception('Subject code is required');
                }

                if (empty($courseId)) {
                    throw new \Exception('Course is required');
                }

                // Check if course exists and belongs to institution
                $course = $this->courseModel->getCourseById($courseId, $institutionId);
                if (!$course) {
                    throw new \Exception('Course not found or does not belong to your institution');
                }

                // Check if code already exists (excluding current subject)
                if ($this->subjectModel->codeExists($code, $courseId, $institutionId, $id)) {
                    throw new \Exception('Subject code already exists for this course');
                }

                $data = [
                    'name' => $name,
                    'code' => $code,
                    'course_id' => $courseId,
                    'workload' => !empty($_POST['workload']) ? (int)$_POST['workload'] : null,
                    'semester' => !empty($_POST['semester']) ? (int)$_POST['semester'] : null,
                    'description' => $_POST['description'] ?? null,
                    'user_id' => $_SESSION['user']['id'],
                    'institution_id' => $institutionId,
                    'active' => isset($_POST['active']) ? 1 : 0
                ];

                $this->subjectModel->update($id, $data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Subject updated successfully!'
                ];

                // Redirect back to course detail if coming from there
                if (isset($_POST['return_to_course']) && $_POST['return_to_course']) {
                    $this->redirect('/courses/show/' . $courseId);
                } else {
                    $this->redirect('/subjects');
                }
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Error updating subject: ' . $e->getMessage()
                ];
                $this->redirect('/subjects');
            }
        } else {
            $this->redirect('/subjects');
        }
    }

    /**
     * Delete a subject
     */
    public function delete($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if subject exists
                $subject = $this->subjectModel->getSubjectById($id, $institutionId);
                if (!$subject) {
                    throw new \Exception('Subject not found');
                }

                $courseId = $subject['course_id'];
                $this->subjectModel->delete($id, $institutionId);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Subject deleted successfully!'
                ];

                // If request came from course page, redirect back there
                $referer = $_SERVER['HTTP_REFERER'] ?? '';
                if (strpos($referer, '/courses/show/') !== false) {
                    $this->redirect('/courses/show/' . $courseId);
                } else {
                    $this->redirect('/subjects');
                }
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Error deleting subject: ' . $e->getMessage()
                ];
                $this->redirect('/subjects');
            }
        } else {
            $this->redirect('/subjects');
        }
    }

    /**
     * Get subjects by course
     */
    public function getByCourse($courseId)
    {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $subjects = $this->subjectModel->getSubjectsByCourse($courseId, $institutionId);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'subjects' => $subjects]);
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}