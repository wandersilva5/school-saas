<?php

namespace App\Controllers;

use App\Models\SliderImage;

class HomeInstitutionController extends BaseController
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

        // Dados para os cards do dashboard
        $dashboardData = [
            'total_users' => 150,
            'total_courses' => 25,
            'active_classes' => 12,
            'new_students' => 45
        ];

        // Fetch slider images for the current institution
        $sliderModel = new SliderImage();
        $sliderImages = $sliderModel->getSliderImagesByInstitution($user['institution_id']);

        return $this->render('dashboard/home-institution', [
            'user' => $user,
            'pageTitle' => "Dashboard",
            'currentPage' => 'dashboard',
            'dashboardData' => $dashboardData,
            'sliderImages' => $sliderImages
        ]);
    }
}