<?php

namespace App\Controllers;

use App\Models\SliderImage;

class DashboardInstitutionController extends BaseController
{
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        $user = $_SESSION['user'];
        $institutionId = $user['institution_id'];
        error_log("DashboardController: Usuário encontrado: " . print_r($user, true));

        // Dados para os cards do dashboard - informações fictícias relevantes
        $dashboardData = [
            'total_students' => 428,
            'active_students' => 412,
            'total_teachers' => 38,
            'total_classes' => 25,
            'avg_attendance' => 94.2, // Percentual de frequência média
            'upcoming_events' => 3,
            'recent_absences' => 15,
            'pending_payments' => 28
        ];

        // Dados de performance acadêmica para gráfico
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

        // Dados de distribuição de alunos por turma
        $classDistribution = [
            ['turma' => '1º Ano A', 'alunos' => 32],
            ['turma' => '1º Ano B', 'alunos' => 30],
            ['turma' => '2º Ano A', 'alunos' => 28],
            ['turma' => '2º Ano B', 'alunos' => 26],
            ['turma' => '3º Ano A', 'alunos' => 25],
            ['turma' => '3º Ano B', 'alunos' => 24]
        ];

        // Últimos comunicados enviados
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
            ],
            [
                'titulo' => 'Feira de Ciências',
                'data' => '2025-04-22',
                'conteudo' => 'Inscrições abertas para a Feira de Ciências anual.'
            ]
        ];

        // Dados financeiros (recebimentos mensais)
        $financialData = [
            'labels' => ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho'],
            'expected' => [42000, 42000, 42000, 42000, 42000, 42000],
            'received' => [40800, 41200, 41500, 41000, 39600, 38200]
        ];

        // Indicadores de desempenho (KPIs)
        $performanceKPIs = [
            'attendance_rate' => 94.2, // Taxa de frequência
            'approval_rate' => 91.5,   // Taxa de aprovação
            'dropout_rate' => 2.8,     // Taxa de evasão
            'teacher_satisfaction' => 86.3, // Satisfação dos professores
            'parent_satisfaction' => 82.7,  // Satisfação dos pais
            'academic_growth' => 8.4        // Crescimento acadêmico (%)
        ];

        // Próximos eventos
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
            ],
            [
                'titulo' => 'Passeio Cultural - Teatro Municipal',
                'data' => '2025-04-15',
                'horario' => '09:00 - 13:00'
            ]
        ];

        // Dados de frequência por dia da semana
        $attendanceByWeekday = [
            'labels' => ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'],
            'data' => [96.2, 95.8, 94.1, 93.4, 91.5]
        ];

        

        return $this->render('dashboard/dashboard-institution', [
            'user' => $user,
            'pageTitle' => "Dashboard Institucional",
            'currentPage' => 'dashboard-institution',
            'dashboardData' => $dashboardData,
            'academicPerformance' => $academicPerformance,
            'classDistribution' => $classDistribution,
            'recentAnnouncements' => $recentAnnouncements,
            'financialData' => $financialData,
            'performanceKPIs' => $performanceKPIs,
            'upcomingEvents' => $upcomingEvents,
            'attendanceByWeekday' => $attendanceByWeekday,
        ]);
    }
}