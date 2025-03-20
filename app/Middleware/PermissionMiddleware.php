<?php
// app/Middleware/PermissionMiddleware.php

namespace App\Middleware;

class PermissionMiddleware {
    public function handle($requiredRoles = []) {
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            // Only set the warning message if we're not already on the login page
            $currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if ($currentUrl !== '/login') {
                $this->redirectWithToast('/login', 'warning', 'Você precisa fazer login para acessar esta página.');
                return false;
            }
            return false;
        }
        
        // If the user just logged in, don't show unnecessary messages
        if (isset($_SESSION['just_logged_in'])) {
            unset($_SESSION['just_logged_in']);
            return true;
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