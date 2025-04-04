<?php

namespace App\Router;

class ApiRouter
{
    private $routes;
    private $baseControllerNamespace = 'App\\Controllers\\Api\\';

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * Process the current request and route it to the appropriate controller/action
     */
    public function dispatch()
    {
        // Get the request URI
        $uri = $_SERVER['REQUEST_URI'];
        
        // Handle CORS preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            exit;
        }
        
        // Extract path from URI without query string
        $path = parse_url($uri, PHP_URL_PATH);
        
        // Check if this is an API route
        if (strpos($path, '/api/') !== 0) {
            return false; // Not an API route
        }
        
        // Find matching route
        $route = $this->findRoute($path);
        
        if (!$route) {
            $this->sendJsonError('Route not found', 404);
            return true;
        }
        
        // Check HTTP method
        if (isset($route['method']) && $_SERVER['REQUEST_METHOD'] !== $route['method']) {
            $this->sendJsonError('Method not allowed', 405);
            return true;
        }
        
        // Get controller and action
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        $params = $route['params'] ?? [];
        
        // Instantiate controller
        $controllerClass = $this->baseControllerNamespace . $controllerName;
        
        if (!class_exists($controllerClass)) {
            $this->sendJsonError('Controller not found', 500);
            return true;
        }
        
        $controller = new $controllerClass();
        
        // Check if action exists
        if (!method_exists($controller, $actionName)) {
            $this->sendJsonError('Action not found', 500);
            return true;
        }
        
        // Call the action with params
        try {
            call_user_func_array([$controller, $actionName], $params);
        } catch (\Exception $e) {
            $this->sendJsonError('Error processing request: ' . $e->getMessage(), 500);
        }
        
        return true;
    }
    
    /**
     * Find a matching route for the given path
     */
    private function findRoute($path)
    {
        // Loop through routes
        foreach ($this->routes as $routePattern => $routeInfo) {
            // Skip non-API routes
            if (strpos($routePattern, 'api/') !== 0) {
                continue;
            }
            
            // Convert route pattern with params to regex
            $pattern = $this->patternToRegex($routePattern);
            
            // Check for match
            if (preg_match($pattern, $path, $matches)) {
                // Extract parameters
                array_shift($matches); // Remove full match
                
                // Add params to route info
                $routeInfo['params'] = $matches;
                
                return $routeInfo;
            }
        }
        
        return null;
    }
    
    /**
     * Convert a route pattern like 'api/users/{id}' to a regex
     */
    private function patternToRegex($pattern)
    {
        // Replace {param} with regex capture group
        $pattern = preg_replace('/{([^\/]+)}/', '([^/]+)', $pattern);
        
        // Escape slashes and add start/end markers
        $pattern = '#^' . str_replace('/', '\\/', $pattern) . '$#';
        
        return $pattern;
    }
    
    /**
     * Send a JSON error response
     */
    private function sendJsonError($message, $statusCode = 400)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
}