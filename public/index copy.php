<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/../app/config/Env.php';
// Carrega as variáveis de ambiente
App\Config\Env::load(__DIR__ . '/../.env');

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

// // Obtém a URL atual
// $url = isset($_GET['url']) ? cleanUrl($_GET['url']) : '';

// // Definição das rotas
// $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Middleware global para verificar instituição
$institutionCheck = new \App\Middleware\InstitutionCheck();

// // Rotas que não precisam de verificação de instituição
// $publicRoutes = ['/', '/login', '/register', '/forgot-password'];

// // Verifica se a rota atual precisa de verificação
// if (!in_array($uri, $publicRoutes)) {
//     error_log("Verificando middleware para rota: " . $uri);
//     if (!isset($_SESSION['user'])) {
//         error_log("Usuário não está logado, redirecionando para login");
//         header('Location: /login');
//         exit;
//     }
//     $institutionCheck->handle();
// }

// Obtém a URL atual
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$url = trim($requestUri, '/');

// Sistema de roteamento
$routes = require_once __DIR__ . '/../routes/web.php';

// Rotas públicas
$publicRoutes = [
    '',            // Home page
    'login',       // Login page
    'register',    // Registration page
    'assets'       // Public assets
];

function getRoutePermissions() {
    static $permissionsCache = null;
    
    if ($permissionsCache === null) {
        try {
            $menuModel = new \App\Models\Menu();
            $permissionsCache = $menuModel->getRoutePermissions();
            
            // If permissions are empty, set a toast warning
            if (empty($permissionsCache)) {
                $_SESSION['toast'] = [
                    'type' => 'warning',
                    'message' => 'Nenhuma configuração de permissão encontrada. O acesso pode ser limitado.'
                ];
            }
        } catch (\Exception $e) {
            // Set toast error message
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Erro ao carregar permissões: ' . $e->getMessage()
            ];
            
            // Return empty array if there's an error
            $permissionsCache = [];
        }
    }
    
    return $permissionsCache;
}

$routePermissions = getRoutePermissions();



// Get the current URL
$url = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Check if the current route is public
$isPublicRoute = false;
foreach ($publicRoutes as $publicRoute) {
    if ($url === $publicRoute || strpos($url, $publicRoute . '/') === 0) {
        $isPublicRoute = true;
        break;
    }
}

// If it's not a public route and user isn't logged in, redirect to login
if (!$isPublicRoute && !isset($_SESSION['user'])) {
    // Store toast message
    $_SESSION['toast'] = [
        'type' => 'warning',
        'message' => 'Você precisa fazer login para acessar esta página.'
    ];
    header('Location: /login');
    exit;
}


$routeFound = false;
$routeKey = null;
$params = [];

// Try exact match first
if (isset($routes[$url])) {
    $routeKey = $url;
    $routeFound = true;
} else {
    // Try with parameters
    foreach ($routes as $pattern => $route) {
        $patternParts = explode('/', $pattern);
        $urlParts = explode('/', $url);

        if (count($patternParts) === count($urlParts)) {
            $match = true;
            $extractedParams = [];

            for ($i = 0; $i < count($patternParts); $i++) {
                if (preg_match('/\{([a-z_]+)\}/', $patternParts[$i], $matches)) {
                    // This is a parameter
                    $extractedParams[] = $urlParts[$i];
                } else if ($patternParts[$i] !== $urlParts[$i]) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $routeKey = $pattern;
                $routeFound = true;
                $params = $extractedParams;
                break;
            }
        }
    }
}

// If route exists, check permissions and execute
if ($routeFound) {
    $baseRoute = explode('/', $routeKey)[0]; // First segment
    $isPublicRoute = in_array($baseRoute, $publicRoutes);

    // Check permissions only for non-public routes
    if (!$isPublicRoute) {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            $_SESSION['toast'] = [
                'type' => 'warning',
                'message' => 'Você precisa fazer login para acessar esta página.'
            ];
            header('Location: /login');
            exit;
        }

        // Check role permissions if this route has specific permissions
        if (isset($routePermissions[$baseRoute])) {
            $requiredRoles = $routePermissions[$baseRoute];
            $userRoles = $_SESSION['user']['roles'] ?? [];
            $hasPermission = false;

            // Check if user has any of the required roles
            foreach ($requiredRoles as $role) {
                if (in_array($role, $userRoles)) {
                    $hasPermission = true;
                    break;
                }
            }

            // If no permission, redirect with toast
            if (!$hasPermission) {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => 'Você não tem permissão para acessar esta página.'
                ];
                header('Location: /dashboard');
                exit;
            }
        }
    }

    // If we reach here, user has permission to access the route
    // Instantiate and execute controller
    $controllerName = "\\App\\Controllers\\" . $routes[$routeKey]['controller'];
    $actionName = $routes[$routeKey]['action'];

    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $actionName)) {
            call_user_func_array([$controller, $actionName], $params);
            exit;
        }
    }
}

$controller = new \App\Controllers\HomeController();
$controller->error();
exit;
