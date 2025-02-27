<?php

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
$publicRoutes = ['/login', '/register', '/forgot-password'];

if (!in_array($uri, $publicRoutes)) {
    $institutionCheck->handle();
}

// Sistema de roteamento básico
$routes = [
    '' => ['controller' => 'HomeController', 'action' => 'index'],
    'login' => ['controller' => 'AuthController', 'action' => 'login'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
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