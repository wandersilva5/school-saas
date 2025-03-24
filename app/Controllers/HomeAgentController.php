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

        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        $institution_id = $user['institution_id'];

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

        return $this->render('dashboard/home-agent', [
            'user' => $user,
            'pageTitle' => "Dashboard - Controle de Acesso",
            'currentPage' => 'dashboard',
            'dashboardData' => $dashboardData,
            'recentRecords' => $recentRecords,
            'pendingAuths' => $pendingAuths
        ]);
    }
}