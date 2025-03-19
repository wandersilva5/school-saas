<?php

namespace App\Services;

use App\Models\Menu;

class MenuService
{
    private $menuModel;

    public function __construct()
    {
        $this->menuModel = new Menu();
    }

    public function getUserMenu($userRoles)
    {
        $allMenus = $this->menuModel->getAll();
        $accessibleMenus = [];
        
        foreach ($allMenus as $menu) {
            if ($this->hasAccess($menu, $userRoles)) {
                $accessibleMenus[] = $menu;
            }
        }
        
        return $accessibleMenus;
    }

    public function hasAccess($menu, $userRoles)
    {
        if (empty($menu['required_roles'])) {
            return true; // No roles required, everyone can access
        }
        
        // Handle different format possibilities
        $requiredRoles = $menu['required_roles'];
        if (is_string($requiredRoles)) {
            $requiredRoles = explode(',', $requiredRoles);
            // Clean up any spaces
            $requiredRoles = array_map('trim', $requiredRoles);
        }
        
        // Check if the user has any of the required roles
        foreach ($requiredRoles as $role) {
            if (in_array($role, $userRoles)) {
                return true;
            }
        }
        
        return false;
    }
}