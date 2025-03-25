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
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();
        
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