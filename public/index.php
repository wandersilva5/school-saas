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
function cleanUrl($url) {
    $url = trim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    return $url;
}

// Obtém a URL atual
$url = isset($_GET['url']) ? cleanUrl($_GET['url']) : '';

// Definição das rotas
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Middleware global para verificar instituição
$institutionCheck = new \App\Middleware\InstitutionCheck();

// Rotas que não precisam de verificação de instituição
$publicRoutes = ['/', '/login', '/register', '/forgot-password'];

// Verifica se a rota atual precisa de verificação
if (!in_array($uri, $publicRoutes)) {
    error_log("Verificando middleware para rota: " . $uri);
    if (!isset($_SESSION['user'])) {
        error_log("Usuário não está logado, redirecionando para login");
        header('Location: /login');
        exit;
    }
    $institutionCheck->handle();
}

// Sistema de roteamento básico
$routes = [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'login' => ['controller' => 'AuthController', 'action' => 'login'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    'access-management' => ['controller' => 'AccessManagementController', 'action' => 'index'],
    'access-management/update-roles' => ['controller' => 'AccessManagementController', 'action' => 'updateUserRoles'],
    'access-management/create-user' => ['controller' => 'AccessManagementController', 'action' => 'createUser'],
    'calendar' => ['controller' => 'CalendarController', 'action' => 'index'],
    // Adicione mais rotas conforme necessário
];

// Parse da URL
$urlParts = explode('/', $url);
$routeKey = $urlParts[0];

// Verifica se a rota existe
if (array_key_exists($routeKey, $routes)) {
    $controllerName = "\\App\\Controllers\\" . $routes[$routeKey]['controller'];
    $actionName = $routes[$routeKey]['action'];
    
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
echo '404 - Página não encontrada';