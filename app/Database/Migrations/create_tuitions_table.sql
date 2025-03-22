CREATE TABLE IF NOT EXISTS tuitions (
    id INT NOT NULL AUTO_INCREMENT,
    student_id INT NOT NULL COMMENT 'ID do aluno',
    amount DECIMAL(10,2) NOT NULL COMMENT 'Valor da mensalidade',
    due_date DATE NOT NULL COMMENT 'Data de vencimento',
    paid_at DATETIME DEFAULT NULL COMMENT 'Data do pagamento',
    payment_method ENUM('Boleto', 'Cartão', 'PIX', 'Dinheiro') DEFAULT NULL COMMENT 'Método de pagamento',
    payment_amount DECIMAL(10,2) DEFAULT NULL COMMENT 'Valor pago',
    discount_amount DECIMAL(10,2) DEFAULT 0 COMMENT 'Valor do desconto',
    status ENUM('Pendente', 'Pago', 'Atrasado', 'Cancelado') DEFAULT 'Pendente',
    observation TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Observações',
    institution_id INT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT tuitions_student_fk FOREIGN KEY (student_id) REFERENCES users(id),
    CONSTRAINT tuitions_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para otimização
CREATE INDEX idx_tuitions_student ON tuitions(student_id);
CREATE INDEX idx_tuitions_institution ON tuitions(institution_id);
CREATE INDEX idx_tuitions_due_date ON tuitions(due_date);
CREATE INDEX idx_tuitions_status ON tuitions(status);
