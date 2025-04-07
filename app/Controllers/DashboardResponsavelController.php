<?php

namespace App\Controllers;

use App\Models\SliderImage;
use App\Models\Responsavel;

class DashboardResponsavelController extends BaseController
{
    private $responsavelModel;
    private $sliderModel;

    public function __construct()
    {
        $this->responsavelModel = new Responsavel();
        $this->sliderModel = new SliderImage();
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

        try {
            // Inicializa variáveis com valores padrão
            $alunos = [];
            $financeiro = [];
            $comunicados = [];
            $eventos = [];
            $sliderImages = [];

            $responsavelId = $_SESSION['user']['id'];
            $institutionId = $_SESSION['user']['institution_id'];

            // Verifica se é um responsável válido
            $checkResult = $this->responsavelModel->verificarVinculoResponsavel($responsavelId, $institutionId);
            $hasStudents = !empty($checkResult['count']);

            if (!$hasStudents) {
                return $this->render('dashboard/responsavel', [
                    'pageTitle' => 'Área do Responsável',
                    'alunos' => [],
                    'financeiro' => [],
                    'comunicados' => [],
                    'eventos' => [],
                    'sliderImages' => [],
                    'error' => 'Nenhum aluno encontrado vinculado a este responsável.'
                ]);
            }

            // Busca os alunos vinculados ao responsável
            $alunos = $this->responsavelModel->getAlunosVinculados($responsavelId, $institutionId);

            // Extrai IDs dos alunos para buscar dados financeiros
            $alunosIds = array_column($alunos, 'id');

            // Busca dados financeiros dos alunos
            $financeiro = $this->responsavelModel->getDadosFinanceiros($alunosIds, $institutionId);

            // Busca comunicados recentes
            $comunicados = $this->responsavelModel->getComunicados($institutionId);

            // Busca próximos eventos
            $eventos = $this->responsavelModel->getEventos($institutionId);

            return $this->render('dashboard/responsavel', [
                'pageTitle' => 'Seja bem-vindo(a) Área do Responsável',
                'alunos' => $alunos ?: [],
                'financeiro' => $financeiro ?: [],
                'comunicados' => $comunicados ?: [],
                'eventos' => $eventos ?: [],
            ]);
        } catch (\Exception $e) {
            error_log('Erro detalhado: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
