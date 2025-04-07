<?php
// Simple script to debug API routes

// Load the routes
$webRoutes = require __DIR__ . '/../routes/web.php';
$apiRoutes = require __DIR__ . '/../routes/api.php';

// Display all API routes
echo "<h1>API Routes</h1>";
echo "<pre>";
foreach ($apiRoutes as $route => $config) {
    echo htmlspecialchars($route) . " => " . 
         htmlspecialchars($config['controller'] . '@' . $config['action']) . 
         " [" . ($config['method'] ?? 'ANY') . "]\n";
}
echo "</pre>";

// Check if the login route exists
$loginRoute = 'api/auth/login';
echo "<h2>Login Route</h2>";
if (isset($apiRoutes[$loginRoute])) {
    echo "Found: " . htmlspecialchars($loginRoute) . " => " . 
         htmlspecialchars($apiRoutes[$loginRoute]['controller'] . '@' . 
         $apiRoutes[$loginRoute]['action']);
} else {
    echo "NOT FOUND: The login route '$loginRoute' is not defined in routes/api.php";
}