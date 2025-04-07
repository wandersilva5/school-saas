CREATE TABLE payments (
id INT AUTO_INCREMENT PRIMARY KEY,
student_id INT NOT NULL,
amount DECIMAL(10,2) NOT NULL,
description VARCHAR(255) NOT NULL,
due_date DATE NOT NULL,
reference_month INT NOT NULL,
reference_year INT NOT NULL,
status ENUM('Pendente', 'Pago', 'Atrasado', 'Cancelado') DEFAULT 'Pendente',
boleto_code VARCHAR(100),
boleto_url VARCHAR(255),
discount_amount DECIMAL(10,2) DEFAULT 0,
fine_amount DECIMAL(10,2) DEFAULT 0,
payment_date DATE,
payment_method ENUM('Boleto', 'Cartão', 'PIX', 'Dinheiro'),
payment_amount DECIMAL(10,2),
notes TEXT,
institution_id INT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (student_id) REFERENCES users(id),
FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_payments_student ON payments(student_id)
CREATE INDEX idx_payments_institution ON payments(institution_id)
CREATE INDEX idx_payments_due_date ON payments(due_date)
CREATE INDEX idx_payments_status ON payments(status)