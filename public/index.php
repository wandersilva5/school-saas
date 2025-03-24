<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

// Carrega as variáveis de ambiente (usando apenas uma implementação)
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

// Sistema de roteamento
$routes = require_once __DIR__ . '/../routes/web.php';

// Rotas públicas
$publicRoutes = [
    '',            // Home page
    'login',       // Login page
    'register',    // Registration page
    'assets'       // Public assets
];

// Verifica se a rota atual é pública
$baseRoute = explode('/', $url)[0] ?? '';
$isPublicRoute = in_array($baseRoute, $publicRoutes);

// Middleware para verificar autenticação apenas para rotas privadas
// if (!$isPublicRoute && !isset($_SESSION['user'])) {
//     $_SESSION['toast'] = [
//         'type' => 'warning',
//         'message' => 'Você precisa fazer login para acessar esta página.'
//     ];
//     header('Location: /login');
//     exit;
// }

// Carrega permissões de rotas
function getRoutePermissions()
{
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

// Se o usuário está logado, verifica a instituição
if (!$isPublicRoute && isset($_SESSION['user'])) {
    try {
        $institutionCheck = new \App\Middleware\InstitutionCheck();
        $institutionCheck->handle();
    } catch (\Exception $e) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Erro na validação da instituição: ' . $e->getMessage()
        ];
        header('Location: /login');
        exit;
    }
}

// PROCESSAMENTO DE ROTAS
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

// Se a rota não foi encontrada ou se houve algum erro, exibe a página de erro
$controller = new \App\Controllers\HomeController();
$controller->error();
exit;
