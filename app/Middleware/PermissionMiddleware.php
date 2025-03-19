<?php
// app/Middleware/PermissionMiddleware.php

namespace App\Middleware;

class PermissionMiddleware {
    public function handle($requiredRoles = []) {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            // Not logged in, redirect to login
            $this->redirectWithToast('/login', 'error', 'Você precisa fazer login para acessar esta página.');
            return false;
        }
        
        // If no specific roles required, just being logged in is enough
        if (empty($requiredRoles)) {
            return true;
        }
        
        // Get user roles
        $userRoles = $_SESSION['user']['roles'] ?? [];
        
        // Check if user has at least one of the required roles
        $hasPermission = false;
        foreach ($requiredRoles as $role) {
            if (in_array($role, $userRoles)) {
                $hasPermission = true;
                break;
            }
        }
        
        // If no permission, redirect with toast
        if (!$hasPermission) {
            $this->redirectWithToast('/dashboard', 'error', 'Você não tem permissão para acessar esta página.');
            return false;
        }
        
        return true;
    }
    
    private function redirectWithToast($url, $type, $message) {
        // Store toast data in session for display after redirect
        $_SESSION['toast'] = [
            'type' => $type,
            'message' => $message
        ];
        
        header('Location: ' . $url);
        exit;
    }
}