<?php

namespace App\Controllers\Api;

use App\Models\User;

class AuthController extends ApiBaseController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->handleCorsOptions();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->errorResponse('Method not allowed', 405);
        }

        $data = $this->getRequestBody();
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->errorResponse('Email and password are required');
        }

        try {
            // Check credentials
            $email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
            $password = $data['password'];

            // Authenticate user
            $user = $this->userModel->authenticate($email, $password);

            if (!$user) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            // Generate API token
            $token = $this->generateToken($user);

            // Return user data and token
            return $this->successResponse([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'institution_id' => $user['institution_id'],
                    'roles' => is_string($user['roles']) ? explode(',', $user['roles']) : $user['roles']
                ]
            ], 'Authentication successful');

        } catch (\Exception $e) {
            return $this->errorResponse('Authentication error: ' . $e->getMessage(), 500);
        }
    }

    public function logout()
    {
        // For token-based authentication, client-side logout is typically handled
        // by removing the token from storage. Server-side, we could implement
        // token blacklisting if needed.
        
        return $this->successResponse(null, 'Logout successful');
    }

    public function profile()
    {
        $this->requireAuth();
        
        try {
            $userId = $_SESSION['user']['id'];
            $user = $this->userModel->get($userId);
            
            if (!$user) {
                return $this->errorResponse('User not found', 404);
            }
            
            // Remove sensitive information
            unset($user['password']);
            
            return $this->successResponse([
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving profile: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Generate a JWT token (simplified example)
     * In a real app, use a proper JWT library
     */
    private function generateToken($user)
    {
        // This is a simplified example
        // In a real app, use a proper JWT library like firebase/php-jwt
        
        // Create token payload
        $payload = [
            'sub' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'institution_id' => $user['institution_id'],
            'roles' => $user['roles'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];
        
        // In a real implementation, you would encode this properly with a secure key
        // This is just for demonstration
        $encodedPayload = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', $encodedPayload, 'your_secret_key_here');
        
        return $encodedPayload . '.' . $signature;
    }
}