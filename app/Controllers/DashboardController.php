<?php

namespace App\Controllers;

use App\Helpers\AuthHelper;

class DashboardController extends BaseController
{
    public function index()
    {
        // Debug
        error_log("DashboardController: Verificando autenticação");
        error_log("DashboardController: Sessão atual: " . print_r($_SESSION, true));

        if (!isset($_SESSION['user'])) {
            error_log("DashboardController: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        error_log("DashboardController: Usuário encontrado: " . print_r($user, true));

        $pageTitle = "Dashboard";
        $currentPage = 'dashboard';

        // Dados para os cards do dashboard
        $dashboardData = [
            'total_users' => 150,
            'total_courses' => 25,
            'active_classes' => 12,
            'new_students' => 45
        ];

        return $this->render('dashboard/index', [
            'user' => $user,
            'pageTitle' => $pageTitle,
            'currentPage' => $currentPage,
            'dashboardData' => $dashboardData
        ]);
    }
} 