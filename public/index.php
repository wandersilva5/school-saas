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
    'users/update/{id}' => ['controller' => 'UserController', 'action' => 'update'],

];

// Parse da URL
if ($url) {
    $urlParts = explode('/', $url);

    // Primeiro tente a URL completa
    if (array_key_exists($url, $routes)) {
        $routeKey = $url;
    }
    // Se não encontrar, tente apenas o primeiro segmento
    else if (array_key_exists($urlParts[0], $routes)) {
        $routeKey = $urlParts[0];
    } else {
        $routeKey = null;
    }
} else {
    $routeKey = '';
}

// Verifica se a rota existe
if (array_key_exists($url, $routes)) {
    $controllerName = "\\App\\Controllers\\" . $routes[$url]['controller'];
    $actionName = $routes[$url]['action'];
    
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            // Extrai parâmetros da URL se existirem
            $params = [];
            $patternParts = explode('/', $routeKey);
            $urlParts = explode('/', $url);
            
            for ($i = 0; $i < count($patternParts); $i++) {
                if (isset($patternParts[$i]) && preg_match('/^\{([a-z0-9_]+)\}$/', $patternParts[$i], $matches)) {
                    if (isset($urlParts[$i])) {
                        $params[] = $urlParts[$i];
                    }
                }
            }
            
            // Chama o método do controller com os parâmetros
            call_user_func_array([$controller, $actionName], $params);
            exit;
        }
    }
}

// Se chegou aqui, a rota não foi encontrada
header('HTTP/1.1 404 Not Found');
$errorController = new \App\Controllers\ErrorController();
$errorController->notFound();