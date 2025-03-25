<?php

namespace App\Controllers;

use App\Models\SliderImage;

class HomeInstitutionController extends BaseController
{
    public function index()
    {
        if (!isset($_SESSION['user'])) {
            error_log("Alerta: Usuário não está na sessão");
            header('Location: /login');
            exit;
        }

        // Verify role and institution_id for Responsavel users
        check_responsavel_institution();

        $user = $_SESSION['user'];
        $institutionId = $user['institution_id'];

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

     

        // Fetch slider images for the current institution
        $sliderModel = new SliderImage();
        $sliderImages = $sliderModel->getSliderImagesByInstitution($user['institution_id']);

        return $this->render('home-institution/index', [
            'user' => $user,
            'pageTitle' => "Dashboard Institucional",
            'currentPage' => 'dashboard-institution',
            'recentAnnouncements' => $recentAnnouncements,
            'upcomingEvents' => $upcomingEvents,
            'sliderImages' => $sliderImages
        ]);
    }
}