<?php

return [
    // Authentication endpoints
    'api/auth/login' => ['controller' => 'Api\AuthController', 'action' => 'login', 'method' => 'POST'],
    'api/auth/logout' => ['controller' => 'Api\AuthController', 'action' => 'logout', 'method' => 'POST'],
    'api/auth/profile' => ['controller' => 'Api\AuthController', 'action' => 'profile', 'method' => 'GET'],
    
    // Dashboard data
    'api/dashboard' => ['controller' => 'Api\DashboardController', 'action' => 'index', 'method' => 'GET'],
    'api/dashboard/institution' => ['controller' => 'Api\DashboardController', 'action' => 'institution', 'method' => 'GET'],
    'api/dashboard/responsavel' => ['controller' => 'Api\DashboardController', 'action' => 'responsavel', 'method' => 'GET'],
    'api/dashboard/agent' => ['controller' => 'Api\DashboardController', 'action' => 'agent', 'method' => 'GET'],
    
    // Access Management
    'api/access-management/users' => ['controller' => 'Api\AccessManagementController', 'action' => 'getUsers', 'method' => 'GET'],
    'api/access-management/roles' => ['controller' => 'Api\AccessManagementController', 'action' => 'getRoles', 'method' => 'GET'],
    'api/access-management/institutions' => ['controller' => 'Api\AccessManagementController', 'action' => 'getInstitutions', 'method' => 'GET'],
    
    // Calendar and Events
    'api/calendar/events' => ['controller' => 'Api\CalendarController', 'action' => 'getEvents', 'method' => 'GET'],
    'api/calendar/events/{date}' => ['controller' => 'Api\CalendarController', 'action' => 'getEventsByDay', 'method' => 'GET'],
    'api/calendar/events/{id}' => ['controller' => 'Api\CalendarController', 'action' => 'getEvent', 'method' => 'GET'],
    
    // Institutions
    'api/institutions' => ['controller' => 'Api\InstitutionController', 'action' => 'index', 'method' => 'GET'],
    'api/institutions/{id}' => ['controller' => 'Api\InstitutionController', 'action' => 'show', 'method' => 'GET'],
    'api/institutions/guardian/{guardianId}' => ['controller' => 'Api\InstitutionController', 'action' => 'getInstitutionsForGuardian', 'method' => 'GET'],
    
    // Users
    'api/users' => ['controller' => 'Api\UserController', 'action' => 'index', 'method' => 'GET'],
    'api/users/{id}' => ['controller' => 'Api\UserController', 'action' => 'show', 'method' => 'GET'],
    'api/users/{id}/info' => ['controller' => 'Api\UserController', 'action' => 'getUserInfo', 'method' => 'GET'],

    // Slider Images
    'api/slider-images' => ['controller' => 'Api\SliderImageController', 'action' => 'index', 'method' => 'GET'],
    'api/slider-images/institution/{institutionId}' => ['controller' => 'Api\SliderImageController', 'action' => 'getByInstitution', 'method' => 'GET'],
    
    // Students
    'api/students' => ['controller' => 'Api\StudentController', 'action' => 'index', 'method' => 'GET'],
    'api/students/{id}' => ['controller' => 'Api\StudentController', 'action' => 'show', 'method' => 'GET'],
    'api/students/{id}/info' => ['controller' => 'Api\StudentController', 'action' => 'getInfo', 'method' => 'GET'],
    'api/students/guardian/{guardianId}' => ['controller' => 'Api\StudentController', 'action' => 'getStudentsByGuardian', 'method' => 'GET'],
    
    // Guardians
    'api/guardians' => ['controller' => 'Api\GuardianController', 'action' => 'index', 'method' => 'GET'],
    'api/guardians/{id}' => ['controller' => 'Api\GuardianController', 'action' => 'show', 'method' => 'GET'],
    'api/guardians/{id}/students' => ['controller' => 'Api\GuardianController', 'action' => 'getStudents', 'method' => 'GET'],
    
    // Classes
    'api/classes' => ['controller' => 'Api\ClassController', 'action' => 'index', 'method' => 'GET'],
    'api/classes/{id}' => ['controller' => 'Api\ClassController', 'action' => 'show', 'method' => 'GET'],
    'api/classes/{id}/students' => ['controller' => 'Api\ClassController', 'action' => 'getStudents', 'method' => 'GET'],
    'api/classes/{id}/available-students' => ['controller' => 'Api\ClassController', 'action' => 'getAvailableStudents', 'method' => 'GET'],
    
    // Courses
    'api/courses' => ['controller' => 'Api\CourseController', 'action' => 'index', 'method' => 'GET'],
    'api/courses/{id}' => ['controller' => 'Api\CourseController', 'action' => 'show', 'method' => 'GET'],
    
    // Subjects
    'api/subjects' => ['controller' => 'Api\SubjectController', 'action' => 'index', 'method' => 'GET'],
    'api/subjects/{id}' => ['controller' => 'Api\SubjectController', 'action' => 'show', 'method' => 'GET'],
    'api/subjects/course/{courseId}' => ['controller' => 'Api\SubjectController', 'action' => 'getSubjectsByCourse', 'method' => 'GET'],
    
    // Menus
    'api/menus' => ['controller' => 'Api\MenuController', 'action' => 'index', 'method' => 'GET'],
    'api/menus/role/{roleId}' => ['controller' => 'Api\MenuController', 'action' => 'getMenusByRole', 'method' => 'GET'],
    
    // Access Control specific endpoints
    'api/access-logs' => ['controller' => 'Api\AccessLogController', 'action' => 'getRecentLogs', 'method' => 'GET'],
    'api/pending-authorizations' => ['controller' => 'Api\PendingAuthorizationController', 'action' => 'getPendingAuthorizations', 'method' => 'GET'],
    'api/alerts' => ['controller' => 'Api\AlertController', 'action' => 'getActiveAlerts', 'method' => 'GET'],
    
    // Responsavel specific endpoints
    'api/responsavel/alunos' => ['controller' => 'Api\ResponsavelController', 'action' => 'getAlunosVinculados', 'method' => 'GET'],
    'api/responsavel/financeiro' => ['controller' => 'Api\ResponsavelController', 'action' => 'getDadosFinanceiros', 'method' => 'GET'],
    'api/responsavel/comunicados' => ['controller' => 'Api\ResponsavelController', 'action' => 'getComunicados', 'method' => 'GET'],
    'api/responsavel/eventos' => ['controller' => 'Api\ResponsavelController', 'action' => 'getEventos', 'method' => 'GET'],
];