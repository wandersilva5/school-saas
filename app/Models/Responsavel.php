<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Responsavel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getResponsaveis($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.id, 
                r.nome, 
                r.email, 
                r.telefone,
                r.cpf,
                r.created_at,
                r.active,
                COUNT(a.id) as total_alunos
            FROM responsaveis r
            LEFT JOIN alunos a ON r.id = a.responsavel_id
            WHERE r.institution_id = ? 
            AND r.deleted_at IS NULL
            GROUP BY r.id, r.nome, r.email, r.telefone, r.cpf, r.created_at, r.active
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalResponsaveis($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM responsaveis 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getResponsavelById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM responsaveis 
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAlunosByResponsavel($responsavelId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM alunos 
            WHERE responsavel_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$responsavelId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO responsaveis (nome, email, telefone, cpf, institution_id, created_at, active) 
                VALUES (?, ?, ?, ?, ?, NOW(), 1)
            ");

            $stmt->execute([
                $data['nome'],
                $data['email'],
                $data['telefone'],
                $data['cpf'],
                $data['institution_id']
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao criar responsável: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE responsaveis 
                SET nome = ?, email = ?, telefone = ?, cpf = ?, active = ?, updated_at = NOW() 
                WHERE id = ?
            ");

            $stmt->execute([
                $data['nome'],
                $data['email'],
                $data['telefone'],
                $data['cpf'],
                isset($data['active']) ? 1 : 0,
                $id
            ]);

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao atualizar responsável: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE responsaveis 
                SET deleted_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao excluir responsável: ' . $e->getMessage());
        }
    }

    public function getAlunosVinculados($responsavelId, $institutionId)
    {
        try {
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
            LEFT JOIN user_info si ON si.user_id = u.id
            LEFT JOIN class_students cs ON cs.user_id = u.id
            LEFT JOIN classes c ON c.id = cs.class_id
            WHERE gs.guardian_user_id = ?
            AND gs.institution_id = ?
            AND u.deleted_at IS NULL
        ");

            $stmt->execute([$responsavelId, $institutionId]);
            $alunos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Processar notas dos alunos
            foreach ($alunos as &$aluno) {
                $aluno['notas'] = $this->getNotasAluno($aluno['id'], $institutionId);
            }

            return $alunos;
        } catch (\PDOException $e) {
            error_log('Erro ao buscar alunos vinculados: ' . $e->getMessage());
            return [];
        }
    }

    public function getNotasAluno($alunoId, $institutionId)
    {
        try {
            // Verifica se a tabela subjects existe
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

                $stmtNotas->execute([$alunoId, $institutionId]);
                $notas = $stmtNotas->fetchAll(\PDO::FETCH_KEY_PAIR);
            } else {
                $notas = [
                    'Matemática' => '-',
                    'Português' => '-',
                    'Ciências' => '-'
                ];
            }

            return $notas ?: [];
        } catch (\Exception $e) {
            error_log('Erro ao buscar notas: ' . $e->getMessage());
            return [];
        }
    }

    public function getDadosFinanceiros($alunosIds, $institutionId)
    {
        $financeiro = [];

        foreach ($alunosIds as $alunoId) {
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

                    $stmtFinanceiro->execute([$alunoId]);
                    $financeiro[$alunoId] = $stmtFinanceiro->fetchAll(\PDO::FETCH_ASSOC);
                } else {
                    // Dados fictícios caso a tabela não exista
                    $financeiro[$alunoId] = [
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
                $financeiro[$alunoId] = [];
            }
        }

        return $financeiro;
    }

    public function getComunicados($institutionId, $limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m-%d') as data,
                title as titulo,
                content as descricao
            FROM announcements
            WHERE institution_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ");

            $stmt->execute([$institutionId, $limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Erro ao buscar comunicados: ' . $e->getMessage());
            return [];
        }
    }

    public function getEventos($institutionId, $limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
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
            LIMIT ?
        ");

            $stmt->execute([$institutionId, $limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log('Erro ao buscar eventos: ' . $e->getMessage());
            return [];
        }
    }

    public function verificarVinculoResponsavel($responsavelId, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT COUNT(*) as count, GROUP_CONCAT(student_user_id) as students 
            FROM guardians_students 
            WHERE guardian_user_id = ?
            AND institution_id = ?
            GROUP BY guardian_user_id
        ");

            $stmt->execute([$responsavelId, $institutionId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result;
        } catch (\Exception $e) {
            error_log('Erro ao verificar vínculo de responsável: ' . $e->getMessage());
            return null;
        }
    }
}
