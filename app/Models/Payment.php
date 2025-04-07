<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Payment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all payments for an institution with pagination
     */
    public function getPayments($institutionId, $limit = 10, $offset = 0, $filters = [])
    {
        try {
            $params = [$institutionId];
            $whereConditions = ["p.institution_id = ?"];
            
            // Add filters if provided
            if (!empty($filters['status'])) {
                $whereConditions[] = "p.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['student_id'])) {
                $whereConditions[] = "p.student_id = ?";
                $params[] = $filters['student_id'];
            }
            
            if (!empty($filters['due_date_from'])) {
                $whereConditions[] = "p.due_date >= ?";
                $params[] = $filters['due_date_from'];
            }
            
            if (!empty($filters['due_date_to'])) {
                $whereConditions[] = "p.due_date <= ?";
                $params[] = $filters['due_date_to'];
            }
            
            // Build the WHERE clause
            $whereClause = implode(' AND ', $whereConditions);
            
            $sql = "
                SELECT 
                    p.*,
                    u.name as student_name,
                    u.email as student_email,
                    c.name as class_name
                FROM payments p
                LEFT JOIN users u ON p.student_id = u.id
                LEFT JOIN class_students cs ON u.id = cs.user_id
                LEFT JOIN classes c ON cs.class_id = c.id
                WHERE {$whereClause}
                GROUP BY p.id
                ORDER BY c.name ASC
                ORDER BY p.due_date DESC
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getPayments: " . $e->getMessage());
            throw new \Exception('Erro ao buscar pagamentos: ' . $e->getMessage());
        }
    }

    /**
     * Get the total count of payments for pagination
     */
    public function getTotalPayments($institutionId, $filters = [])
    {
        try {
            $params = [$institutionId];
            $whereConditions = ["institution_id = ?"];
            
            // Add filters if provided
            if (!empty($filters['status'])) {
                $whereConditions[] = "status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['student_id'])) {
                $whereConditions[] = "student_id = ?";
                $params[] = $filters['student_id'];
            }
            
            if (!empty($filters['due_date_from'])) {
                $whereConditions[] = "due_date >= ?";
                $params[] = $filters['due_date_from'];
            }
            
            if (!empty($filters['due_date_to'])) {
                $whereConditions[] = "due_date <= ?";
                $params[] = $filters['due_date_to'];
            }
            
            // Build the WHERE clause
            $whereClause = implode(' AND ', $whereConditions);
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM payments WHERE {$whereClause}");
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (\PDOException $e) {
            error_log("Error in getTotalPayments: " . $e->getMessage());
            throw new \Exception('Erro ao contar pagamentos: ' . $e->getMessage());
        }
    }

    /**
     * Get payment details by ID
     */
    public function getPaymentById($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    u.name as student_name,
                    u.email as student_email,
                    c.name as class_name
                FROM payments p
                LEFT JOIN users u ON p.student_id = u.id
                LEFT JOIN class_students cs ON u.id = cs.user_id
                LEFT JOIN classes c ON cs.class_id = c.id
                WHERE p.id = ? AND p.institution_id = ?
                LIMIT 1
            ");
            $stmt->execute([$id, $institutionId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getPaymentById: " . $e->getMessage());
            throw new \Exception('Erro ao buscar detalhes do pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Create a new payment
     */
    public function createPayment($data)
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    student_id, 
                    amount, 
                    description, 
                    due_date, 
                    reference_month, 
                    reference_year, 
                    status, 
                    boleto_code, 
                    discount_amount, 
                    fine_amount,
                    payment_method,
                    notes,
                    institution_id,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['student_id'],
                $data['amount'],
                $data['description'],
                $data['due_date'],
                $data['reference_month'],
                $data['reference_year'],
                $data['status'] ?? 'Pendente',
                $data['boleto_code'] ?? null,
                $data['discount_amount'] ?? 0,
                $data['fine_amount'] ?? 0,
                $data['payment_method'] ?? null,
                $data['notes'] ?? null,
                $data['institution_id']
            ]);
            
            $paymentId = $this->db->lastInsertId();
            $this->db->commit();
            
            return $paymentId;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error in createPayment: " . $e->getMessage());
            throw new \Exception('Erro ao criar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing payment
     */
    public function updatePayment($id, $data)
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                UPDATE payments 
                SET 
                    student_id = ?,
                    amount = ?,
                    description = ?,
                    due_date = ?,
                    reference_month = ?,
                    reference_year = ?,
                    status = ?,
                    boleto_code = ?,
                    discount_amount = ?,
                    fine_amount = ?,
                    payment_method = ?,
                    notes = ?,
                    updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");
            
            $result = $stmt->execute([
                $data['student_id'],
                $data['amount'],
                $data['description'],
                $data['due_date'],
                $data['reference_month'],
                $data['reference_year'],
                $data['status'],
                $data['boleto_code'] ?? null,
                $data['discount_amount'] ?? 0,
                $data['fine_amount'] ?? 0,
                $data['payment_method'] ?? null,
                $data['notes'] ?? null,
                $id,
                $data['institution_id']
            ]);
            
            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error in updatePayment: " . $e->getMessage());
            throw new \Exception('Erro ao atualizar pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Mark a payment as paid
     */
    public function markAsPaid($id, $data)
    {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                UPDATE payments 
                SET 
                    status = 'Pago',
                    payment_date = ?,
                    payment_method = ?,
                    payment_amount = ?,
                    updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");
            
            $result = $stmt->execute([
                $data['payment_date'] ?? date('Y-m-d'),
                $data['payment_method'],
                $data['payment_amount'],
                $id,
                $data['institution_id']
            ]);
            
            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error in markAsPaid: " . $e->getMessage());
            throw new \Exception('Erro ao marcar pagamento como pago: ' . $e->getMessage());
        }
    }

    /**
     * Delete a payment
     */
    public function deletePayment($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM payments WHERE id = ? AND institution_id = ?");
            return $stmt->execute([$id, $institutionId]);
        } catch (\PDOException $e) {
            error_log("Error in deletePayment: " . $e->getMessage());
            throw new \Exception('Erro ao excluir pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Get payments for a specific student
     */
    public function getStudentPayments($studentId, $institutionId, $limit = 10, $offset = 0)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payments 
                WHERE student_id = ? AND institution_id = ?
                ORDER BY due_date DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$studentId, $institutionId, $limit, $offset]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getStudentPayments: " . $e->getMessage());
            throw new \Exception('Erro ao buscar pagamentos do aluno: ' . $e->getMessage());
        }
    }

    /**
     * Get payment statistics for dashboard
     */
    public function getPaymentStats($institutionId)
    {
        try {
            $stats = [];
            
            // Total amount pending
            $stmt = $this->db->prepare("
                SELECT SUM(amount) as total_pending
                FROM payments
                WHERE institution_id = ? AND status = 'Pendente'
            ");
            $stmt->execute([$institutionId]);
            $stats['total_pending'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_pending'] ?? 0;
            
            // Total amount paid
            $stmt = $this->db->prepare("
                SELECT SUM(payment_amount) as total_paid
                FROM payments
                WHERE institution_id = ? AND status = 'Pago'
            ");
            $stmt->execute([$institutionId]);
            $stats['total_paid'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_paid'] ?? 0;
            
            // Overdue payments count
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as overdue_count
                FROM payments
                WHERE institution_id = ? AND status = 'Pendente' AND due_date < CURDATE()
            ");
            $stmt->execute([$institutionId]);
            $stats['overdue_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['overdue_count'] ?? 0;
            
            // Payments due in the next 15 days
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as upcoming_count
                FROM payments
                WHERE institution_id = ? AND status = 'Pendente' 
                AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 15 DAY)
            ");
            $stmt->execute([$institutionId]);
            $stats['upcoming_count'] = $stmt->fetch(PDO::FETCH_ASSOC)['upcoming_count'] ?? 0;
            
            return $stats;
        } catch (\PDOException $e) {
            error_log("Error in getPaymentStats: " . $e->getMessage());
            throw new \Exception('Erro ao buscar estatísticas de pagamento: ' . $e->getMessage());
        }
    }

    /**
     * Generate boleto for a payment
     */
    public function generateBoleto($paymentId, $institutionId)
    {
        try {
            // First, get the payment details
            $payment = $this->getPaymentById($paymentId, $institutionId);
            
            if (!$payment) {
                throw new \Exception('Pagamento não encontrado');
            }
            
            // Here you would integrate with the boleto library
            // This is just a placeholder - actual implementation depends on the library used
            $boletoCode = 'BOL' . str_pad($paymentId, 8, '0', STR_PAD_LEFT) . date('YmdHis');
            
            // Update the payment with the boleto code
            $stmt = $this->db->prepare("
                UPDATE payments 
                SET boleto_code = ?, updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");
            $stmt->execute([$boletoCode, $paymentId, $institutionId]);
            
            return $boletoCode;
        } catch (\PDOException $e) {
            error_log("Error in generateBoleto: " . $e->getMessage());
            throw new \Exception('Erro ao gerar boleto: ' . $e->getMessage());
        }
    }

    /**
     * Get students list for payment selection dropdown
     */
    public function getStudentsForPayment($institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.name,
                    u.email,
                    c.name as class_name
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id AND r.name = 'Aluno'
                LEFT JOIN class_students cs ON u.id = cs.user_id
                LEFT JOIN classes c ON cs.class_id = c.id
                WHERE u.institution_id = ? AND u.active = 1
                GROUP BY u.id
                ORDER BY u.name
            ");
            $stmt->execute([$institutionId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getStudentsForPayment: " . $e->getMessage());
            throw new \Exception('Erro ao buscar alunos: ' . $e->getMessage());
        }
    }

    /**
     * Generate monthly payments for all active students
     */
    public function generateMonthlyPayments($institutionId, $data)
    {
        try {
            $this->db->beginTransaction();
            
            // Get all active students
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.name
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id AND r.name = 'Aluno'
                WHERE u.institution_id = ? AND u.active = 1
            ");
            $stmt->execute([$institutionId]);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $counter = 0;
            $errors = [];
            
            foreach ($students as $student) {
                try {
                    // Check if a payment already exists for this student and month/year
                    $checkStmt = $this->db->prepare("
                        SELECT COUNT(*) as count 
                        FROM payments 
                        WHERE student_id = ? 
                        AND reference_month = ? 
                        AND reference_year = ? 
                        AND institution_id = ?
                    ");
                    $checkStmt->execute([
                        $student['id'], 
                        $data['reference_month'], 
                        $data['reference_year'], 
                        $institutionId
                    ]);
                    
                    if ($checkStmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
                        // Skip if payment already exists
                        continue;
                    }
                    
                    // Create payment for this student
                    $paymentData = [
                        'student_id' => $student['id'],
                        'amount' => $data['amount'],
                        'description' => $data['description'] . ' - ' . $student['name'],
                        'due_date' => $data['due_date'],
                        'reference_month' => $data['reference_month'],
                        'reference_year' => $data['reference_year'],
                        'status' => 'Pendente',
                        'discount_amount' => $data['discount_amount'] ?? 0,
                        'institution_id' => $institutionId
                    ];
                    
                    $this->createPayment($paymentData);
                    $counter++;
                } catch (\Exception $e) {
                    $errors[] = "Erro para {$student['name']}: {$e->getMessage()}";
                }
            }
            
            $this->db->commit();
            
            return [
                'success' => true,
                'count' => $counter,
                'errors' => $errors
            ];
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Error in generateMonthlyPayments: " . $e->getMessage());
            throw new \Exception('Erro ao gerar mensalidades: ' . $e->getMessage());
        }
    }

    
}