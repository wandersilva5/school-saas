<?php

namespace App\Controllers\Api;

use App\Models\SliderImage;
use App\Models\AccessLog;
use App\Models\PendingAuthorization;
use App\Models\Alert;
use App\Models\Responsavel;

class DashboardController extends ApiBaseController
{
    public function __construct()
    {
        $this->handleCorsOptions();
    }

    public function index()
    {
        $this->requireAuth();
        
        // Base dashboard for TI users
        if (in_array('TI', $_SESSION['user']['roles'])) {
            return $this->tiDashboard();
        }
        
        // Default to institution dashboard
        return $this->institution();
    }

    private function tiDashboard()
    {
        try {
            $user = $_SESSION['user'];
            
            // Sample dashboard data - in a real implementation, fetch this from appropriate models
            $dashboardData = [
                'total_users' => 150,
                'total_courses' => 25,
                'active_classes' => 12,
                'new_students' => 45
            ];
            
            return $this->successResponse([
                'dashboard_data' => $dashboardData,
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving dashboard data: ' . $e->getMessage(), 500);
        }
    }

    public function institution()
    {
        $this->requireAuth();
        
        try {
            $user = $_SESSION['user'];
            $institutionId = $user['institution_id'];
            
            // Simplified dashboard data - in a real implementation, fetch from database
            $dashboardData = [
                'total_students' => 428,
                'active_students' => 412,
                'total_teachers' => 38,
                'total_classes' => 25,
                'avg_attendance' => 94.2,
                'upcoming_events' => 3,
                'recent_absences' => 15,
                'pending_payments' => 28
            ];
            
            // Academic performance data
            $academicPerformance = [
                'labels' => ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
                'datasets' => [
                    [
                        'label' => 'Média Geral',
                        'data' => [7.2, 7.5, 7.8, 7.6, 8.0, 8.2]
                    ],
                    [
                        'label' => 'Meta',
                        'data' => [7.0, 7.0, 7.0, 7.0, 7.0, 7.0]
                    ]
                ]
            ];
            
            // Recent announcements
            $recentAnnouncements = [
                [
                    'titulo' => 'Reunião de Pais e Mestres',
                    'data' => '2025-03-25',
                    'conteudo' => 'Reunião trimestral para discussão do desempenho dos alunos.'
                ],
                [
                    'titulo' => 'Semana de Provas',
                    'data' => '2025-04-10',
                    'conteudo' => 'Calendário das avaliações do primeiro trimestre.'
                ]
            ];
            
            // Upcoming events
            $upcomingEvents = [
                [
                    'titulo' => 'Reunião Pedagógica',
                    'data' => '2025-03-28',
                    'horario' => '14:00 - 17:00'
                ],
                [
                    'titulo' => 'Início das Inscrições para Reforço',
                    'data' => '2025-04-01',
                    'horario' => '08:00 - 18:00'
                ]
            ];
            
            // Fetch slider images
            $sliderModel = new SliderImage();
            $sliderImages = $sliderModel->getSliderImagesByInstitution($institutionId);
            
            return $this->successResponse([
                'dashboard_data' => $dashboardData,
                'academic_performance' => $academicPerformance,
                'announcements' => $recentAnnouncements,
                'upcoming_events' => $upcomingEvents,
                'slider_images' => $sliderImages,
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving institution dashboard data: ' . $e->getMessage(), 500);
        }
    }

    public function responsavel()
    {
        $this->requireAuth();
        
        try {
            // Verify user is a guardian
            if (!in_array('Responsavel', $_SESSION['user']['roles'])) {
                return $this->errorResponse('Access denied', 403);
            }
            
            $responsavelId = $_SESSION['user']['id'];
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Initialize models
            $responsavelModel = new Responsavel();
            $sliderModel = new SliderImage();
            
            // Check if guardian has linked students
            $checkResult = $responsavelModel->verificarVinculoResponsavel($responsavelId, $institutionId);
            
            if (empty($checkResult['count'])) {
                return $this->successResponse([
                    'has_students' => false,
                    'alunos' => [],
                    'financeiro' => [],
                    'comunicados' => [],
                    'eventos' => [],
                    'slider_images' => []
                ], 'No students linked to this guardian');
            }
            
            // Get linked students
            $alunos = $responsavelModel->getAlunosVinculados($responsavelId, $institutionId);
            
            // Get financial data for students
            $alunosIds = array_column($alunos, 'id');
            $financeiro = $responsavelModel->getDadosFinanceiros($alunosIds, $institutionId);
            
            // Get announcements
            $comunicados = $responsavelModel->getComunicados($institutionId);
            
            // Get upcoming events
            $eventos = $responsavelModel->getEventos($institutionId);
            
            // Get slider images
            $sliderImages = $sliderModel->getSliderImagesByInstitution($institutionId);
            
            return $this->successResponse([
                'has_students' => true,
                'alunos' => $alunos,
                'financeiro' => $financeiro,
                'comunicados' => $comunicados,
                'eventos' => $eventos,
                'slider_images' => $sliderImages
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving guardian dashboard data: ' . $e->getMessage(), 500);
        }
    }

    public function agent()
    {
        $this->requireAuth();
        
        try {
            // Verify user is an access control agent
            if (!in_array('Agente de controle', $_SESSION['user']['roles'])) {
                return $this->errorResponse('Access denied', 403);
            }
            
            $institutionId = $_SESSION['user']['institution_id'];
            
            // Initialize models
            $accessLogModel = new AccessLog();
            $authModel = new PendingAuthorization();
            $alertModel = new Alert();
            
            // Get dashboard data
            $dashboardData = [
                'entradas_hoje' => $accessLogModel->getTodayEntriesCount($institutionId),
                'saidas_hoje' => $accessLogModel->getTodayExitsCount($institutionId),
                'visitantes' => $accessLogModel->getTodayVisitorsCount($institutionId),
                'alertas' => $alertModel->getActiveAlertsCount($institutionId)
            ];
            
            // Get recent access logs
            $recentRecords = $accessLogModel->getRecentLogs($institutionId, 10);
            
            // Get pending authorizations
            $pendingAuths = $authModel->getPendingAuthorizations($institutionId);
            
            return $this->successResponse([
                'dashboard_data' => $dashboardData,
                'recent_records' => $recentRecords,
                'pending_authorizations' => $pendingAuths,
                'user' => $_SESSION['user']
            ]);
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving agent dashboard data: ' . $e->getMessage(), 500);
        }
    }
}