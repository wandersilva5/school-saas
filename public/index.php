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

// Debug route access
error_log("Accessing route: " . $url);

// IMPORTANTE: Tratar rota de logout explicitamente
if ($url === 'logout') {
    $controller = new \App\Controllers\AuthController();
    $controller->logout();
    exit;
}

// Sistema de roteamento
$routes = require_once __DIR__ . '/../routes/web.php';

// Rotas públicas ou especiais que não precisam de verificação
$specialRoutes = [
    '',                    // Home page
    'login',               // Login page
    'logout',              // Logout explicitly handled
    'register',            // Registration page
    'assets',              // Public assets
    'institution/list',    // Lista de instituições
    'institution/select',  // Seleção de instituição
    'select-institution',  // Alias para seleção
];

// Verifica se a rota atual é especial
$isSpecialRoute = false;
foreach ($specialRoutes as $route) {
    if ($url === $route || strpos($url, $route . '/') === 0) {
        $isSpecialRoute = true;
        break;
    }
}

// Middleware para verificar autenticação apenas para rotas privadas
// Não verifica para rotas especiais
if (!$isSpecialRoute && !isset($_SESSION['user'])) {
    $_SESSION['toast'] = [
        'type' => 'warning',
        'message' => 'Você precisa fazer login para acessar esta página.'
    ];
    header('Location: /login');
    exit;
}

// Carrega permissões de rotas
function getRoutePermissions()
{
    static $permissionsCache = null;

    if ($permissionsCache === null) {
        try {
            $menuModel = new \App\Models\Menu();
            $permissionsCache = $menuModel->getRoutePermissions();
        } catch (\Exception $e) {
            // Return empty array if there's an error
            $permissionsCache = [];
        }
    }

    return $permissionsCache;
}

$routePermissions = getRoutePermissions();

// Verificação de instituição - pulada para rotas especiais
if (!$isSpecialRoute && isset($_SESSION['user'])) {
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

// If route exists, execute it
if ($routeFound) {
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
error_log("Rota não encontrada: " . $url);
$controller = new \App\Controllers\ErrorController();
$controller->notFound();
exit;