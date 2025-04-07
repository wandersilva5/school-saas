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
        error_log("Dispatching API route for: " . $_SERVER['REQUEST_URI']);
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);

        // Get the request URI
        $uri = $_SERVER['REQUEST_URI'];

        // Handle CORS preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
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

        if (!$route) {
            error_log("No route found for: " . $path);
            error_log("Available routes: " . print_r(array_keys($this->routes), true));
        }

        return true;
    }

    /**
     * Find a matching route for the given path
     */
    // Update the findRoute method in ApiRouter.php
    // Improved findRoute method
    private function findRoute($path)
    {
        // Remove leading slash if present
        $path = ltrim($path, '/');

        // Exact match check
        if (isset($this->routes[$path])) {
            return $this->routes[$path];
        }

        // Check for pattern-based routes
        foreach ($this->routes as $pattern => $routeInfo) {
            // Skip non-API routes
            if (strpos($pattern, 'api/') !== 0) {
                continue;
            }

            // Convert route pattern to regex
            $regex = $this->patternToRegex($pattern);

            // Check for match
            if (preg_match($regex, $path, $matches)) {
                // Remove the full match
                array_shift($matches);

                // Add params to route info
                $routeInfo['params'] = $matches;
                return $routeInfo;
            }
        }

        // Log the failure for debugging
        error_log("API route not found for path: $path");
        error_log("Available routes: " . implode(', ', array_keys($this->routes)));

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
