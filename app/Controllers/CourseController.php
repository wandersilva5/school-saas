<?php

namespace App\Controllers;

use App\Models\Course;

class CourseController extends BaseController
{
    private $courseModel;

    public function __construct()
    {
        $this->courseModel = new Course();
    }

    /**
     * Display the list of courses
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
            $courses = $this->courseModel->getCourses($institutionId, $limit, $offset);
            $totalCourses = $this->courseModel->getTotalCourses($institutionId);
            $totalPages = ceil($totalCourses / $limit);

            $this->render('courses/index', [
                'pageTitle' => 'Listagem dos Cursos',
                'courses' => $courses,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'currentSection' => 'courses' // For active sidebar item
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Error loading courses: ' . $e->getMessage()
            ];

            $this->render('courses/index', [
                'pageTitle' => 'Course Management',
                'courses' => [],
                'currentPage' => 1,
                'totalPages' => 1,
                'currentSection' => 'courses'
            ]);
        }
    }

    /**
     * Display a single course
     */
    public function show($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $course = $this->courseModel->getCourseById($id, $institutionId);

            if (!$course) {
                throw new \Exception('Course not found');
            }

            $this->render('courses/show', [
                'pageTitle' => 'Detalhes do Curso',
                'course' => $course,
            ]);
        } catch (\Exception $e) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => $e->getMessage()
            ];
            $this->redirect('/courses/index');
        }
    }

    /**
     * Store a new course
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

                if (empty($name)) {
                    throw new \Exception('Course name is required');
                }

                if (empty($code)) {
                    throw new \Exception('Course code is required');
                }

                // Check if code already exists
                if ($this->courseModel->codeExists($code, $institutionId)) {
                    throw new \Exception('Course code already exists');
                }

                $data = [
                    'name' => $name,
                    'code' => $code,
                    'description' => $_POST['description'] ?? null,
                    'workload' => !empty($_POST['workload']) ? (int)$_POST['workload'] : null,
                    'duration' => $_POST['duration'] ?? null,
                    'requirements' => $_POST['requirements'] ?? null,
                    'institution_id' => $institutionId,
                    'active' => isset($_POST['active']) ? 1 : 0
                ];

                $this->courseModel->create($data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Course created successfully!'
                ];
                $this->redirect('/courses');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Error creating course: ' . $e->getMessage()
                ];
                $this->redirect('/courses');
            }
        } else {
            $this->redirect('/courses');
        }
    }

    /**
     * Display the form to edit a course
     */
    public function edit($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        try {
            $institutionId = $_SESSION['user']['institution_id'];
            $course = $this->courseModel->getCourseById($id, $institutionId);

            if (!$course) {
                throw new \Exception('Course not found');
            }

            $this->render('courses/edit', [
                'pageTitle' => 'Edit Course',
                'course' => $course,
                'currentSection' => 'courses'
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
     * Update an existing course
     */
    public function update($id)
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if course exists
                $course = $this->courseModel->getCourseById($id, $institutionId);
                if (!$course) {
                    throw new \Exception('Course not found');
                }

                // Form data validation
                $name = trim($_POST['name'] ?? '');
                $code = trim($_POST['code'] ?? '');

                if (empty($name)) {
                    throw new \Exception('Course name is required');
                }

                if (empty($code)) {
                    throw new \Exception('Course code is required');
                }

                // Check if code already exists (excluding current course)
                if ($this->courseModel->codeExists($code, $institutionId, $id)) {
                    throw new \Exception('Course code already exists');
                }

                $data = [
                    'name' => $name,
                    'code' => $code,
                    'description' => $_POST['description'] ?? null,
                    'workload' => !empty($_POST['workload']) ? (int)$_POST['workload'] : null,
                    'duration' => $_POST['duration'] ?? null,
                    'requirements' => $_POST['requirements'] ?? null,
                    'institution_id' => $institutionId,
                    'active' => isset($_POST['active']) ? 1 : 0
                ];

                $this->courseModel->update($id, $data);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Course updated successfully!'
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/courses');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Error updating course: ' . $e->getMessage()
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/courses');
            }
        } else {
            $this->redirect('/courses');
        }
    }

    /**
     * Delete a course
     */
    public function delete($id)
    {
        if (!isset($_SESSION['user'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            $this->redirect('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $institutionId = $_SESSION['user']['institution_id'];

                // Check if course exists
                $course = $this->courseModel->getCourseById($id, $institutionId);
                if (!$course) {
                    throw new \Exception('Course not found');
                }

                $this->courseModel->delete($id, $institutionId);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Course deleted successfully!'
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true]);
                    exit;
                }

                $this->redirect('/courses');
            } catch (\Exception $e) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Error deleting course: ' . $e->getMessage()
                ];

                // If the request was AJAX, return JSON response
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                    exit;
                }

                $this->redirect('/courses');
            }
        } else {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                http_response_code(405);
                echo json_encode(['error' => 'Method Not Allowed']);
                exit;
            }
            $this->redirect('/courses');
        }
    }

    /**
     * Get course by ID for AJAX requests
     */
    public function getById()
    {
        if (!isset($_SESSION['user'])) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
            try {
                $id = $_GET['id'];
                $institutionId = $_SESSION['user']['institution_id'];

                $course = $this->courseModel->getCourseById($id, $institutionId);

                if (!$course) {
                    header('Content-Type: application/json');
                    http_response_code(404);
                    echo json_encode(['error' => 'Course not found']);
                    exit;
                }

                header('Content-Type: application/json');
                echo json_encode($course);
            } catch (\Exception $e) {
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            exit;
        }

        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }
}
