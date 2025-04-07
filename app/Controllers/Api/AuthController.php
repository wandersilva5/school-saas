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
        error_log("Login method called in AuthController");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Method not allowed: " . $_SERVER['REQUEST_METHOD']);
            return $this->errorResponse('Method not allowed', 405);
        }

        $data = $this->getRequestBody();
        error_log("Request body: " . print_r($data, true));

        // Check if we received the required data
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->errorResponse('Email and password are required', 400);
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

            // Return user data and token with safe role handling
            return $this->successResponse([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'institution_id' => $user['institution_id'] ?? null,
                    'roles' => isset($user['roles']) 
                        ? (is_string($user['roles']) 
                            ? explode(',', $user['roles']) 
                            : $user['roles'])
                        : []
                ]
            ], 'Authentication successful');
        } catch (\Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
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
        // Create token payload
        $payload = [
            'sub' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'institution_id' => $user['institution_id'] ?? null,
            'roles' => $user['roles'] ?? [],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // 24 hours
        ];

        // Encode the payload
        $encodedPayload = base64_encode(json_encode($payload));

        // Generate signature
        $signature = hash_hmac('sha256', $encodedPayload, 'your_secret_key_here');

        // Return the complete token
        return $encodedPayload . '.' . $signature;
    }
}