<?php
// app/config/routes.php or similar file where you define your routes

// Define API routes
$routes = [
    // Auth routes
    'api/login'   => ['controller' => 'Api\AuthController','action' => 'login', 'method' => 'POST'],
    'api/logout'  => ['controller' => 'Api\AuthController','action' => 'logout', 'method' => 'POST'],
    'api/profile' => ['controller' => 'Api\AuthController','action' => 'profile', 'method' => 'GET'],
    
    // Other API routes you may need
    'api/dashboard'              => ['controller' => 'Api\DashboardController', 'action' => 'index', 'method' => 'GET'],
    '/api/dashboard/institution' => ['controller' => 'Api\DashboardInstitutionController', 'action' => 'institution', 'method' => 'GET'],
    '/api/dashboard/guardian'    => ['controller' => 'Api\DashboardGuardianController', 'action' => 'responsavel', 'method' => 'GET'],
    '/api/dashboard/agent'       => ['controller' => 'Api\DashboardAgentController', 'action' => 'agente', 'method' => 'GET'],
    
    // Rotas para cursos e alunos
    'api/courses'      => ['controller' => 'Api\CourseController', 'action' => 'index', 'method' => 'GET'],
    'api/courses/{id}' => ['controller' => 'Api\CourseController', 'action' => 'show', 'method' => 'GET'],
    
    // Rotas para alunos
    'api/students'      => ['controller' => 'Api\StudentController', 'action' => 'index', 'method' => 'GET'],
    'api/students/{id}' => ['controller' => 'Api\StudentController', 'action' => 'show', 'method' => 'GET'],
    'api/students'      => ['controller' => 'Api\StudentController', 'action' => 'store', 'method' => 'POST'],

    // Rotas para Calendário
    '/api/calendar/events'        => ['controller' => 'Api\CalendarController', 'action' => 'events', 'method' => 'GET'],
    '/api/calendar/events/{date}' => ['controller' => 'Api\CalendarController', 'action' => 'event', 'method' => 'GET'],

    // Rotas para Slider Images
    '/api/slider-images/institution/{institutionId}' => ['controller' => 'Api\SliderImageController', 'action' => 'institution', 'method' => 'GET'],
    
];

// Create API router if this file is included in the entry point
if (defined('INIT_API_ROUTER')) {
    $apiRouter = new \App\Router\ApiRouter($routes);
    $apiRouter->dispatch();
}

// Make routes available for import elsewhere
return $routes;
