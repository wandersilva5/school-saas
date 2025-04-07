<?php
// api-routes.php - Place this in your public folder

// Turn on error reporting for debugging only - remove in production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>API Routes Debug</h1>";

// This assumes you have access to your routes array somewhere
// You might need to adjust this to how your routes are actually defined
require_once __DIR__ . '/../app/config/routes.php'; // Adjust path as needed

// Display the routes (assuming $routes is the variable name)
if (isset($routes) && is_array($routes)) {
    echo "<h2>Defined Routes</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Pattern</th><th>Controller</th><th>Action</th><th>Method</th></tr>";
    
    foreach ($routes as $pattern => $route) {
        echo "<tr>";
        echo "<td>".htmlspecialchars($pattern)."</td>";
        echo "<td>".htmlspecialchars($route['controller'] ?? 'N/A')."</td>";
        echo "<td>".htmlspecialchars($route['action'] ?? 'N/A')."</td>";
        echo "<td>".htmlspecialchars($route['method'] ?? 'ANY')."</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Could not access routes configuration.</p>";
}

// Check for a specific route
$testRoute = '/api/login';
echo "<h2>Testing Route: {$testRoute}</h2>";

// Simulate how the router would match this route
echo "<p>URL path to match: <strong>{$testRoute}</strong></p>";

if (isset($routes[$testRoute])) {
    echo "<p style='color: green;'>✓ Exact route match found!</p>";
    echo "<pre>";
    print_r($routes[$testRoute]);
    echo "</pre>";
} else {
    echo "<p style='color: orange;'>⚠ No exact route match. Testing pattern matching...</p>";
    
    $matchFound = false;
    foreach ($routes as $pattern => $routeInfo) {
        // Skip non-API routes
        if (strpos($pattern, 'api/') !== 0) {
            continue;
        }
        
        // Convert route pattern to regex similar to your router
        $pattern = preg_replace('/{([^\/]+)}/', '([^/]+)', $pattern);
        $regex = '#^' . str_replace('/', '\\/', $pattern) . '$#';
        
        // Test if our route matches this pattern
        $testRouteTrimmed = ltrim($testRoute, '/');
        if (preg_match($regex, $testRouteTrimmed)) {
            echo "<p style='color: green;'>✓ Pattern match found for: {$pattern}</p>";
            echo "<pre>";
            print_r($routeInfo);
            echo "</pre>";
            $matchFound = true;
            break;
        }
    }
    
    if (!$matchFound) {
        echo "<p style='color: red;'>✗ No matching route found for {$testRoute}</p>";
    }
}

// Check the actual request processing
echo "<h2>Current Request Info</h2>";
echo "<pre>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "</pre>";