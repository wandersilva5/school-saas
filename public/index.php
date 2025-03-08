<?php

// Inicia a sessão no topo do arquivo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis de ambiente
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Função para limpar a URL
function cleanUrl($url)
{
    $url = trim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    return $url;
}

// Obtém a URL atual
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = trim($requestUri, '/');

// Rotas que não precisam de verificação de instituição
$publicRoutes = ['/', '/login', '/register', '/forgot-password'];

// Sistema de roteamento básico
$routes = [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'login' => ['controller' => 'AuthController', 'action' => 'login'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],

    // Rotas para o painel de controle
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    'dashboard-institution' => ['controller' => 'HomeInstitutionController', 'action' => 'index'],

    // Rotas para gerenciamento de acesso
    'access-management' => ['controller' => 'AccessManagementController', 'action' => 'index'],
    'access-management/update-roles' => ['controller' => 'AccessManagementController', 'action' => 'updateUserRoles'],
    'access-management/create-user' => ['controller' => 'AccessManagementController', 'action' => 'createUser'],

    // Rotas para calendário
    'calendar' => ['controller' => 'CalendarController', 'action' => 'index'],

    // Rotas para instituições
    'institution' => ['controller' => 'InstitutionController', 'action' => 'index'],
    'institution/store' => ['controller' => 'InstitutionController', 'action' => 'store'],
    'institution/update/{id}' => ['controller' => 'InstitutionController', 'action' => 'update'],
    
    // Rotas para usuários das instituições
    'users' => ['controller' => 'UserController', 'action' => 'index'],
    'users/get/{id}' => ['controller' => 'UserController', 'action' => 'get'],
    'users/store' => ['controller' => 'UserController', 'action' => 'store'],
    'users/update' => ['controller' => 'UserController', 'action' => 'update'],
];

// Verifica se a rota atual precisa de verificação
if (!in_array($requestUri, $publicRoutes)) {
    if (!isset($_SESSION['user'])) {
        header('Location: /login');
        exit;
    }
    $institutionCheck = new \App\Middleware\InstitutionCheck();
    $institutionCheck->handle();
}

// Verifica se a rota existe
if (array_key_exists($url, $routes)) {
    $controllerName = "\\App\\Controllers\\" . $routes[$url]['controller'];
    $actionName = $routes[$url]['action'];
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            call_user_func([$controller, $actionName]);
            exit;
        }
    }
}

// Se chegou aqui, a rota não foi encontrada
header('HTTP/1.1 404 Not Found');
$errorController = new \App\Controllers\ErrorController();
$errorController->notFound();