<?php

namespace App\Helpers;

use App\Config\Database;

class MenuPermissionHelper
{
    /**
     * Checks if the current user has permission to access a specific route
     * 
     * @param string $route The route to check
     * @return bool True if user has permission
     */
    public static function userHasAccess($route)
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['roles'])) {
            return false;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT required_roles 
            FROM menus 
            WHERE route = ?
            LIMIT 1
        ");
        $stmt->execute([$route]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }
        
        $requiredRoles = explode(',', $result['required_roles']);
        $userRoles = $_SESSION['user']['roles'];
        
        // Check if user has any of the required roles
        foreach ($requiredRoles as $role) {
            if (in_array(trim($role), $userRoles)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Verifies access permission and redirects if not allowed
     * 
     * @param string $route The route to check
     * @return void
     */
    public static function verifyAccess($route)
    {
        if (!self::userHasAccess($route)) {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => 'Você não tem permissão para acessar esta página.'
            ];
            header('Location: /dashboard-institution');
            exit;
        }
    }
}