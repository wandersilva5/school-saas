<?php
// public/api/index.php or similar file that processes API requests

// Set up error handling for API
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Start a session if needed for authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define constant to trigger router initialization in routes file
define('INIT_API_ROUTER', true);

// Load the routes configuration (which also creates and dispatches the router)
require_once __DIR__ . '/../../app/config/routes.php';

// If we get here, it means no route was matched
http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'error',
    'message' => 'API endpoint not found'
]);
exit;