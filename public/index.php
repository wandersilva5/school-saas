<?php
// Inicia a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/Env.php';

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

// Carregar arquivos de ambiente
App\Config\Env::load(__DIR__ . '/../.env');

// Definir timezone
date_default_timezone_set('America/Sao_Paulo');

// Carregar rotas
$webRoutes = require __DIR__ . '/../routes/web.php';

// Verificar se o arquivo api.php existe
$apiRoutes = [];
if (file_exists(__DIR__ . '/../routes/api.php')) {
    $apiRoutes = require __DIR__ . '/../routes/api.php';
}

// Combinar rotas
$routes = array_merge($webRoutes, $apiRoutes);

// Configurar cabeçalhos CORS para API
if (strpos($_SERVER['REQUEST_URI'], '/api/') === 0) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

// Verificar se a classe ApiRouter existe
$apiRoutingEnabled = class_exists('App\\Router\\ApiRouter');

// Inicializar o roteador da API se disponível
$routeHandled = false;
if ($apiRoutingEnabled) {
    $apiRouter = new App\Router\ApiRouter($routes);
    $routeHandled = $apiRouter->dispatch();
}

// Se não for uma rota de API, use o roteador web existente
if (!$routeHandled) {
    // Obter URL da requisição
    $requestUri = $_SERVER['REQUEST_URI'];
    
    // Remover parâmetros GET da URL
    $uri = parse_url($requestUri, PHP_URL_PATH);
    
    // Remover a barra inicial para obter o padrão de rota
    $uri = ltrim($uri, '/');
    
    // Se não houver rota, usar a rota padrão
    if (empty($uri)) {
        $uri = '';
    }
    
    // Verificar se existe uma rota definida para a URI
    if (isset($routes[$uri])) {
        $route = $routes[$uri];
    } else {
        // Verificar rotas com parâmetros
        foreach ($routes as $pattern => $handler) {
            if (strpos($pattern, '{') !== false) {
                $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
                $regex = '#^' . $regex . '$#';
                
                if (preg_match($regex, $uri, $matches)) {
                    array_shift($matches); // Remove a correspondência completa
                    $route = $handler;
                    break;
                }
            }
        }
    }
    
    // Se não encontrou rota, use a rota de erro
    if (!isset($route)) {
        $route = $routes['error'] ?? null;
    }
    
    // Verificar se a rota foi encontrada
    if ($route) {
        // Extrai o controlador e a ação
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        
        // Constrói o nome completo da classe do controlador
        $controllerClass = "\\App\\Controllers\\{$controllerName}";
        
        // Verifica se a classe do controlador existe
        if (class_exists($controllerClass)) {
            // Instancia o controlador
            $controller = new $controllerClass();
            
            // Verifica se o método da ação existe
            if (method_exists($controller, $actionName)) {
                // Chama a ação do controlador com parâmetros, se existirem
                if (isset($matches)) {
                    call_user_func_array([$controller, $actionName], $matches);
                } else {
                    // Sem parâmetros
                    $controller->$actionName();
                }
            } else {
                // Método não encontrado
                echo "404 - Action not found: {$actionName}";
            }
        } else {
            // Controlador não encontrado
            echo "404 - Controller not found: {$controllerName}";
        }
    } else {
        // Rota não encontrada
        echo "404 - Route not found";
    }
}

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
