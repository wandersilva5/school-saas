<?php

namespace App\Controllers\Api;

class ApiBaseController
{
    /**
     * Send a JSON response with appropriate headers
     */
    protected function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        echo json_encode($data);
        exit;
    }

    /**
     * Send an error response
     */
    protected function errorResponse($message, $statusCode = 400)
    {
        return $this->jsonResponse([
            'status' => 'error',
            'message' => $message
        ], $statusCode);
    }

    /**
     * Send a success response
     */
    protected function successResponse($data = null, $message = 'Operation successful')
    {
        return $this->jsonResponse([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Get JSON request body
     */
    protected function getRequestBody()
    {
        $rawInput = file_get_contents('php://input');
        error_log("Raw request body: " . $rawInput); // Debug log
        
        if (empty($rawInput)) {
            return [];
        }
        
        $data = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON decode error: " . json_last_error_msg());
            return [];
        }
        
        return $data;
    }
    
    /**
     * Handle CORS Options requests
     */
    protected function handleCorsOptions()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            exit;
        }
    }
    
    /**
     * Check if user is authenticated via API token
     * Simple placeholder implementation
     */
    protected function requireAuth()
    {
        // Get Authorization header
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        // Check token format
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $this->errorResponse('Authentication required', 401);
        }
        
        $token = $matches[1];
        
        // Simple token validation
        $parts = explode('.', $token);
        if (count($parts) < 2) {
            $this->errorResponse('Invalid token format', 401);
        }
        
        try {
            // Decode the payload
            $payload = json_decode(base64_decode($parts[0]), true);
            
            // Check for expiration
            if (!isset($payload['exp']) || $payload['exp'] < time()) {
                $this->errorResponse('Token expired', 401);
            }
            
            // Set user in session (simple approach)
            $_SESSION['user'] = [
                'id' => $payload['sub'],
                'name' => $payload['name'],
                'email' => $payload['email'],
                'institution_id' => $payload['institution_id'],
                'roles' => $payload['roles']
            ];
            
            return true;
        } catch (\Exception $e) {
            $this->errorResponse('Invalid token: ' . $e->getMessage(), 401);
        }
    }
}