<?php

namespace App\Controllers;

use App\Models\SliderImage;
use App\Config\Database;

class DashboardResponsavelController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        try {
            // Inicializa variáveis com valores padrão
            $alunos = [];
            $financeiro = [];
            $comunicados = [];
            $eventos = [];
            $sliderImages = [];

            $responsavelId = $_SESSION['user']['id'];
            $institutionId = $_SESSION['user']['institution_id'];

            // Verifica se é um responsável válido e debug
            $stmtCheck = $this->db->prepare("
                SELECT COUNT(*) as count, GROUP_CONCAT(student_user_id) as students 
                FROM guardians_students 
                WHERE guardian_user_id = ?
                AND institution_id = ?
                GROUP BY guardian_user_id
            ");
            $stmtCheck->execute([$responsavelId, $institutionId]);
            $checkResult = $stmtCheck->fetch(\PDO::FETCH_ASSOC);
            
            error_log('Check Query: ' . $stmtCheck->queryString);
            error_log('Check Params: guardian_user_id=' . $responsavelId . ', institution_id=' . $institutionId);
            error_log('Check Result: ' . print_r($checkResult, true));

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
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.name as nome,
                    COALESCE(si.registration_number, 'Não informado') as matricula,
                    c.name as turma,
                    c.shift as turno,
                    '100' as frequencia,
                    COALESCE(si.birth_date, CURRENT_DATE) as data_nascimento,
                    si.health_observations as observacoes_saude
                FROM users u
                INNER JOIN guardians_students gs ON gs.student_user_id = u.id
                LEFT JOIN student_info si ON si.user_id = u.id
                LEFT JOIN class_students cs ON cs.student_id = u.id
                LEFT JOIN classes c ON c.id = cs.class_id
                WHERE gs.guardian_user_id = ?
                AND gs.institution_id = ?
                AND u.deleted_at IS NULL
            ");
            
            // Debug das queries
            error_log('Guardian Query: ' . $stmt->queryString);
            error_log('Guardian Params: guardian_user_id=' . $responsavelId . ', institution_id=' . $institutionId);

            $stmt->execute([$responsavelId, $institutionId]);
            $alunos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            error_log('Alunos encontrados: ' . print_r($alunos, true));

            // Se não encontrou alunos mas tem vínculo, pode ser problema com as outras tabelas
            if (empty($alunos) && $hasStudents) {
                error_log('Tem vínculo mas não retornou alunos - possível problema com JOINS');
                
                // Tenta buscar só os dados básicos
                $stmtBasic = $this->db->prepare("
                    SELECT u.* 
                    FROM users u
                    INNER JOIN guardians_students gs ON gs.student_user_id = u.id
                    WHERE gs.guardian_user_id = ?
                    AND gs.institution_id = ?
                    AND u.deleted_at IS NULL
                ");
                $stmtBasic->execute([$responsavelId, $institutionId]);
                $basicAlunos = $stmtBasic->fetchAll(\PDO::FETCH_ASSOC);
                error_log('Dados básicos dos alunos: ' . print_r($basicAlunos, true));
            }

            // Para debug
            error_log('Responsável ID: ' . $responsavelId);
            error_log('Institution ID: ' . $institutionId);
            error_log('Query executada: ' . $stmt->queryString);
            error_log('Parâmetros: ' . print_r([$responsavelId, $institutionId], true));
            error_log('Alunos encontrados: ' . print_r($alunos, true));

            // Busca as notas dos alunos
            foreach ($alunos as &$aluno) {
                try {
                    // Primeiro verifica se a tabela subjects existe
                    $stmtCheck = $this->db->prepare("SHOW TABLES LIKE 'subjects'");
                    $stmtCheck->execute();
                    $subjectsExist = $stmtCheck->rowCount() > 0;

                    if ($subjectsExist) {
                        $stmtNotas = $this->db->prepare("
                            SELECT 
                                s.name as disciplina,
                                COALESCE(g.grade, 0) as nota
                            FROM subjects s
                            LEFT JOIN grades g ON g.subject_id = s.id 
                                AND g.student_id = ?
                                AND g.deleted_at IS NULL
                            WHERE s.institution_id = ?
                            LIMIT 3
                        ");
                        
                        $stmtNotas->execute([$aluno['id'], $institutionId]);
                        $notas = $stmtNotas->fetchAll(\PDO::FETCH_KEY_PAIR);
                    } else {
                        $notas = [
                            'Matemática' => '-',
                            'Português' => '-',
                            'Ciências' => '-'
                        ];
                    }
                    $aluno['notas'] = $notas ?: [];
                } catch (\Exception $e) {
                    error_log('Erro ao buscar notas: ' . $e->getMessage());
                    $aluno['notas'] = [];
                }
            }

            // Busca dados financeiros dos alunos
            $financeiro = [];
            foreach ($alunos as $aluno) {
                try {
                    // Verifica se a tabela existe
                    $stmtCheck = $this->db->prepare("SHOW TABLES LIKE 'tuitions'");
                    $stmtCheck->execute();
                    $tuitionsExist = $stmtCheck->rowCount() > 0;

                    if ($tuitionsExist) {
                        $stmtFinanceiro = $this->db->prepare("
                            SELECT 
                                DATE_FORMAT(due_date, '%m/%Y') as mes,
                                amount as valor,
                                DATE_FORMAT(due_date, '%d/%m/%Y') as vencimento,
                                CASE 
                                    WHEN paid_at IS NOT NULL THEN 'Pago'
                                    WHEN due_date < CURRENT_DATE THEN 'Atrasado'
                                    ELSE 'Em aberto'
                                END as status
                            FROM tuitions
                            WHERE student_id = ?
                            AND deleted_at IS NULL
                            ORDER BY due_date DESC
                            LIMIT 6
                        ");
                        
                        $stmtFinanceiro->execute([$aluno['id']]);
                        $financeiro[$aluno['id']] = $stmtFinanceiro->fetchAll(\PDO::FETCH_ASSOC);
                    } else {
                        // Dados fictícios caso a tabela não exista
                        $financeiro[$aluno['id']] = [
                            [
                                'mes' => date('m/Y'),
                                'valor' => 0.00,
                                'vencimento' => date('d/m/Y'),
                                'status' => 'Pendente'
                            ]
                        ];
                    }
                } catch (\Exception $e) {
                    error_log('Erro ao buscar dados financeiros: ' . $e->getMessage());
                    $financeiro[$aluno['id']] = [];
                }
            }

            // Busca comunicados recentes
            $stmtComunicados = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m-%d') as data,
                    title as titulo,
                    content as descricao
                FROM announcements
                WHERE institution_id = ?
                ORDER BY created_at DESC
                LIMIT 5
            ");
            
            $stmtComunicados->execute([$institutionId]);
            $comunicados = $stmtComunicados->fetchAll(\PDO::FETCH_ASSOC);

            // Busca próximos eventos
            $stmtEventos = $this->db->prepare("
                SELECT 
                    DATE_FORMAT(date, '%Y-%m-%d') as data,
                    title as titulo,
                    CONCAT(
                        DATE_FORMAT(start_time, '%H:%i'),
                        ' - ',
                        DATE_FORMAT(end_time, '%H:%i')
                    ) as horario
                FROM events
                WHERE institution_id = ?
                AND date >= CURRENT_DATE
                ORDER BY date ASC
                LIMIT 5
            ");
            
            $stmtEventos->execute([$institutionId]);
            $eventos = $stmtEventos->fetchAll(\PDO::FETCH_ASSOC);

            // Busca imagens do slider
            $sliderModel = new SliderImage();
            $sliderImages = $sliderModel->getSliderImagesByInstitution($institutionId);

            return $this->render('dashboard/responsavel', [
                'pageTitle' => 'Área do Responsável',
                'alunos' => $alunos ?: [],
                'financeiro' => $financeiro ?: [],
                'comunicados' => $comunicados ?: [],
                'eventos' => $eventos ?: [],
                'sliderImages' => $sliderImages ?: []
            ]);

        } catch (\Exception $e) {
            error_log('Erro detalhado: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return $this->render('dashboard/responsavel', [
                'pageTitle' => 'Área do Responsável',
                'alunos' => [],
                'financeiro' => [],
                'comunicados' => [],
                'eventos' => [],
                'sliderImages' => [],
                'error' => 'Erro ao carregar dados: ' . $e->getMessage()
            ]);
        }
    }
}
