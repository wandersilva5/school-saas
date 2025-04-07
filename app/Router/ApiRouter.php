<?php

namespace App\Router;

class ApiRouter
{
    private $routes;
    private $baseControllerNamespace = 'App\\Controllers\\Api\\';

    public function __construct(array $routes)
    {
        $this->routes = $routes;
        error_log("API Router initialized with " . count($routes) . " routes");
    }

    public function dispatch()
    {
        // Get the request URI and method
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Debug log
        error_log("API Request: $method $uri");
        
        // Handle CORS preflight requests
        if ($method === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            exit;
        }
        
        // Extract path from URI without query string
        $path = parse_url($uri, PHP_URL_PATH);
        $path = ltrim($path, '/');
        
        // Log the path we're looking for
        error_log("Looking for route matching path: $path");
        
        // Check if this is an API route
        if (strpos($path, 'api/') !== 0) {
            error_log("Not an API route: $path");
            return false;
        }
        
        // Find matching route
        $route = $this->findRoute($path);
        
        if (!$route) {
            error_log("No route found for: $path");
            error_log("Available routes: " . implode(', ', array_keys($this->routes)));
            $this->sendJsonError('Route not found: ' . $path, 404);
            return true;
        }
        
        // Debug the found route
        error_log("Found route: " . json_encode($route));
        
        // Check HTTP method if specified
        if (isset($route['method']) && $method !== $route['method']) {
            error_log("Method not allowed: $method (expected: {$route['method']})");
            $this->sendJsonError('Method not allowed', 405);
            return true;
        }
        
        // Get controller and action
        $controllerName = $route['controller'];
        $actionName = $route['action'];
        $params = $route['params'] ?? [];
        
        // Debug controller and action
        error_log("Controller: $controllerName, Action: $actionName");
        
        // Check for fully qualified controller name
        if (strpos($controllerName, '\\') === 0) {
            $controllerClass = $controllerName;
        } else {
            // Construct controller class name with namespace
            $controllerClass = $this->baseControllerNamespace . $controllerName;
        }
        
        error_log("Controller class: $controllerClass");
        
        // Check if controller class exists
        if (!class_exists($controllerClass)) {
            error_log("Controller not found: $controllerClass");
            $this->sendJsonError('Controller not found: ' . $controllerName, 500);
            return true;
        }
        
        // Instantiate controller
        try {
            $controller = new $controllerClass();
        } catch (\Throwable $e) {
            error_log("Error instantiating controller: " . $e->getMessage());
            $this->sendJsonError('Error creating controller: ' . $e->getMessage(), 500);
            return true;
        }
        
        // Check if action exists
        if (!method_exists($controller, $actionName)) {
            error_log("Action not found: $actionName");
            $this->sendJsonError('Action not found: ' . $actionName, 500);
            return true;
        }
        
        // Call the action with params
        try {
            error_log("Calling action: $actionName with params: " . json_encode($params));
            call_user_func_array([$controller, $actionName], $params);
        } catch (\Throwable $e) {
            error_log("Error in controller action: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->sendJsonError('Error processing request: ' . $e->getMessage(), 500);
        }
        
        return true;
    }
    
    private function findRoute($path)
    {
        // Log that we're searching for a route
        error_log("Searching for route: $path");
        
        // Look for exact match
        if (isset($this->routes[$path])) {
            error_log("Found exact route match: $path");
            return $this->routes[$path];
        }
        
        // Then try pattern routes
        foreach ($this->routes as $pattern => $routeInfo) {
            // Skip non-API routes
            if (strpos($pattern, 'api/') !== 0) {
                continue;
            }
            
            // Check for pattern match
            if (strpos($pattern, '{') !== false) {
                $patternRegex = $this->patternToRegex($pattern);
                
                error_log("Testing pattern: $pattern with regex: $patternRegex");
                
                if (preg_match($patternRegex, $path, $matches)) {
                    error_log("Found pattern match: $pattern");
                    
                    // Remove the full match
                    array_shift($matches);
                    
                    // Add params to route info
                    $routeInfo['params'] = $matches;
                    
                    return $routeInfo;
                }
            }
        }
        
        error_log("No route found for path: $path");
        return null;
    }
    
    private function patternToRegex($pattern)
    {
        // Replace {param} with regex capture group
        $pattern = preg_replace('/\{([^\/]+)\}/', '([^/]+)', $pattern);
        
        // Escape forward slashes and add start/end markers
        $pattern = '#^' . str_replace('/', '\\/', $pattern) . '$#';
        
        return $pattern;
    }
    
    private function sendJsonError($message, $statusCode = 400)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
}