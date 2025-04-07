<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/Env.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load environment files
App\Config\Env::load(__DIR__ . '/../.env');

// Set timezone
date_default_timezone_set('America/Sao_Paulo');

// Function to clean URL
function cleanUrl($url)
{
    $url = trim($url, '/');
    $url = filter_var($url, FILTER_SANITIZE_URL);
    return $url;
}

// Get current URL
$requestUri = $_SERVER['REQUEST_URI'];
$url = trim(parse_url($requestUri, PHP_URL_PATH), '/');

// Debug log
error_log("Accessing route: " . $url);

// Handle logout explicitly
if ($url === 'logout') {
    $controller = new \App\Controllers\AuthController();
    $controller->logout();
    exit;
}

// Load routes
$webRoutes = require __DIR__ . '/../routes/web.php';

// Check if api.php exists
$apiRoutes = [];
if (file_exists(__DIR__ . '/../routes/api.php')) {
    $apiRoutes = require __DIR__ . '/../routes/api.php';
}

// Combine routes
$routes = array_merge($webRoutes, $apiRoutes);

// Set CORS headers for API requests
if (strpos($url, 'api/') === 0) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }
}

// Initialize route handling flag
$routeHandled = false;

// Check if the ApiRouter class exists
$apiRoutingEnabled = class_exists('App\\Router\\ApiRouter');

// Handle API routes if it's an API request
if (strpos($url, 'api/') === 0 && $apiRoutingEnabled) {
    $apiRouter = new App\Router\ApiRouter($routes);
    $routeHandled = $apiRouter->dispatch();
    
    // If API router couldn't handle it, return a 404 JSON response
    if (!$routeHandled) {
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Route not found']);
        exit;
    }
    
    // If the API route was handled, exit
    exit;
}

// Special routes that don't need authentication check
$specialRoutes = [
    '',                    // Home page
    'login',               // Login page
    'logout',              // Logout explicitly handled
    'register',            // Registration page
    'assets',              // Public assets
    'institution/list',    // Institution list
    'institution/select',  // Institution selection
    'select-institution',  // Alias for selection
];

// Check if current route is special
$isSpecialRoute = false;
foreach ($specialRoutes as $route) {
    if ($url === $route || strpos($url, $route . '/') === 0) {
        $isSpecialRoute = true;
        break;
    }
}

// Authentication middleware for private routes
if (!$isSpecialRoute && !isset($_SESSION['user'])) {
    $_SESSION['toast'] = [
        'type' => 'warning',
        'message' => 'Você precisa fazer login para acessar esta página.'
    ];
    header('Location: /login');
    exit;
}

// Load route permissions
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

// Institution check middleware - skipped for special routes
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

// WEB ROUTE PROCESSING
// First try exact match
if (isset($routes[$url])) {
    $route = $routes[$url];
    
    $controllerName = $route['controller'];
    $actionName = $route['action'];
    
    // Construct full controller class name
    $controllerClass = "\\App\\Controllers\\{$controllerName}";
    
    // Check if controller class exists
    if (class_exists($controllerClass)) {
        // Instantiate controller
        $controller = new $controllerClass();
        
        // Check if action method exists
        if (method_exists($controller, $actionName)) {
            // Call controller action without parameters
            $controller->$actionName();
            exit;
        }
    }
}

// Try routes with parameters
$matches = [];
foreach ($routes as $pattern => $route) {
    if (strpos($pattern, '{') !== false) {
        $regex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';
        
        if (preg_match($regex, $url, $matches)) {
            array_shift($matches); // Remove the complete match
            
            $controllerName = $route['controller'];
            $actionName = $route['action'];
            
            // Construct full controller class name
            $controllerClass = "\\App\\Controllers\\{$controllerName}";
            
            // Check if controller class exists
            if (class_exists($controllerClass)) {
                // Instantiate controller
                $controller = new $controllerClass();
                
                // Check if action method exists
                if (method_exists($controller, $actionName)) {
                    // Call controller action with parameters
                    call_user_func_array([$controller, $actionName], $matches);
                    exit;
                } else {
                    // Method not found
                    echo "404 - Action not found: {$actionName}";
                    exit;
                }
            } else {
                // Controller not found
                echo "404 - Controller not found: {$controllerName}";
                exit;
            }
        }
    }
}

// If no route was found, use the error route
if (isset($routes['error'])) {
    $route = $routes['error'];
    $controllerName = $route['controller'];
    $actionName = $route['action'];
    
    $controllerClass = "\\App\\Controllers\\{$controllerName}";
    
    if (class_exists($controllerClass)) {
        $controller = new $controllerClass();
        if (method_exists($controller, $actionName)) {
            $controller->$actionName();
            exit;
        }
    }
}

// If we get here, display a 404 error
error_log("Route not found: " . $url);
$controller = new \App\Controllers\ErrorController();
$controller->notFound();
exit;