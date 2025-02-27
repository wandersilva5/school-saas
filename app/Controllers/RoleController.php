<?php

namespace App\Controllers;

use App\Models\Role;

class RoleController
{
    private Role $roleModel;

    public function __construct()
    {
        $this->roleModel = new Role();
    }

    public function index()
    {
        $roles = $this->roleModel->getAllRoles();
        // Passar os dados para a view
        return $roles;
    }

    public function show(int $id)
    {
        $role = $this->roleModel->getRoleById($id);
        // Passar os dados para a view
        return $role;
    }

    public function listByInstitution(int $institutionId)
    {
        $roles = $this->roleModel->getRolesByInstitution($institutionId);
        // Passar os dados para a view
        return $roles;
    }
}