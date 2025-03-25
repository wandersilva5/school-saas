<?php

namespace App\Helpers;

class UserRoleHelper
{
    /**
     * Check if the current user has the specified role
     * 
     * @param string $role The role to check for
     * @return bool Whether the user has the specified role
     */
    public static function hasRole($role)
    {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['roles'])) {
            return false;
        }
        
        $userRoles = $_SESSION['user']['roles'];
        
        // Check if roles is array or string
        if (is_string($userRoles)) {
            $userRoles = explode(',', $userRoles);
        }
        
        return in_array($role, $userRoles);
    }
    
    /**
     * Verify that a user with 'Responsavel' role has an institution_id set
     * 
     * @return bool|string Returns true if check passes, or redirect path if it fails
     */
    public static function verifyResponsavelInstitution()
    {
        if (!isset($_SESSION['user'])) {
            return '/login';
        }
        
        // If user has 'Responsavel' role and no institution_id
        if (self::hasRole('Responsavel') && 
            (!isset($_SESSION['user']['institution_id']) || empty($_SESSION['user']['institution_id']))) {
            return '/institution/list';
        }
        
        return true;
    }
    
    /**
     * Check and redirect based on role and required institution
     * 
     * @return void
     */
    public static function checkResponsavelRedirect()
    {
        $redirectPath = self::verifyResponsavelInstitution();
        
        if ($redirectPath !== true) {
            // Set toast message if needed
            $_SESSION['toast'] = [
                'type' => 'info',
                'message' => 'Por favor, selecione uma instituição'
            ];
            
            // Redirect
            header('Location: ' . $redirectPath);
            exit;
        }
    }
}