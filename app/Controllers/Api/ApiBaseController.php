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
     * Check if user is authenticated via API token
     */
    protected function requireAuth()
    {
        // Get Authorization header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        // Check token format
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $this->errorResponse('Authentication required', 401);
        }
        
        $token = $matches[1];
        
        // Validate token (simplified example - implement proper JWT or token validation)
        // This is just a placeholder - you should implement proper token validation
        if (!$this->validateToken($token)) {
            $this->errorResponse('Invalid or expired token', 401);
        }
        
        return true;
    }
    
    /**
     * Validate token (placeholder implementation)
     */
    private function validateToken($token)
    {
        // Implement proper token validation logic here
        // For example, verify JWT signature, check expiration, etc.
        
        // For development/example purposes only:
        // In a real implementation, you should use a proper JWT library
        
        try {
            // Placeholder for token validation logic
            // In a real implementation, decode and verify the token
            
            // Set user session based on token
            $_SESSION['user'] = [
                'id' => 1, // This should come from the token
                'institution_id' => 1, // This should come from the token
                'roles' => ['TI'] // This should come from the token
            ];
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get JSON request body
     */
    protected function getRequestBody()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
    
    /**
     * Handle OPTIONS requests for CORS
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
}