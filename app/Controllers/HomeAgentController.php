<?php

namespace App\Controllers;

use App\Models\SliderImage;
use App\Models\AccessLog;
use App\Models\PendingAuthorization;
use App\Models\Alert;

class HomeAgentController extends BaseController
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
        $institution_id = $user['institution_id'];
        error_log("DashboardController: Usuário encontrado: " . print_r($user, true));

        // Instanciar modelos
        $accessLogModel = new AccessLog();
        $authModel = new PendingAuthorization();
        $alertModel = new Alert();

        // Dados para os cards do dashboard
        $dashboardData = [
            'entradas_hoje' => $accessLogModel->getTodayEntriesCount($institution_id),
            'saidas_hoje' => $accessLogModel->getTodayExitsCount($institution_id),
            'visitantes' => $accessLogModel->getTodayVisitorsCount($institution_id),
            'alertas' => $alertModel->getActiveAlertsCount($institution_id)
        ];

        // Buscar registros recentes
        $recentRecords = $accessLogModel->getRecentLogs($institution_id, 10);

        // Buscar autorizações pendentes
        $pendingAuths = $authModel->getPendingAuthorizations($institution_id);

        // Buscar imagens do slider
        $sliderModel = new SliderImage();
        $sliderImages = $sliderModel->getSliderImagesByInstitution($institution_id);

        return $this->render('dashboard/home-agent', [
            'user' => $user,
            'pageTitle' => "Dashboard - Controle de Acesso",
            'currentPage' => 'dashboard',
            'dashboardData' => $dashboardData,
            'sliderImages' => $sliderImages,
            'recentRecords' => $recentRecords,
            'pendingAuths' => $pendingAuths
        ]);
    }
}