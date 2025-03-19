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
        return $this->menuModel->getMenusByRole($userRoles);
    }

    public function hasAccess($menu, $userRoles)
    {
        $requiredRoles = explode(',', $menu['required_roles']);
        return count(array_intersect($requiredRoles, $userRoles)) > 0;
    }
}
