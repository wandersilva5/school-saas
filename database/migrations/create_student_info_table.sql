CREATE TABLE IF NOT EXISTS student_info (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL COMMENT 'Referência ao usuário aluno',
    registration_number VARCHAR(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Número de matrícula',
    birth_date DATE NOT NULL COMMENT 'Data de nascimento',
    gender ENUM('M', 'F', 'O') NOT NULL COMMENT 'Gênero',
    blood_type VARCHAR(3) COMMENT 'Tipo sanguíneo',
    address_street VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Rua/Avenida',
    address_number VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT 'Número',
    address_complement VARCHAR(100) COLLATE utf8mb4_unicode_ci COMMENT 'Complemento',
    address_district VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bairro',
    address_city VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cidade',
    address_state CHAR(2) NOT NULL COMMENT 'Estado (UF)',
    address_zipcode VARCHAR(10) NOT NULL COMMENT 'CEP',
    emergency_contact VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contato de emergência',
    emergency_phone VARCHAR(20) NOT NULL COMMENT 'Telefone de emergência',
    health_insurance VARCHAR(100) COLLATE utf8mb4_unicode_ci COMMENT 'Plano de saúde',
    health_observations TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Observações de saúde (alergias, medicações, etc)',
    previous_school VARCHAR(255) COLLATE utf8mb4_unicode_ci COMMENT 'Escola anterior',
    observation TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Observações gerais',
    institution_id INT NOT NULL COMMENT 'ID da instituição',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_registration (registration_number, institution_id),
    UNIQUE KEY unique_student (user_id, institution_id),
    CONSTRAINT student_info_user_fk FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT student_info_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para otimização
CREATE INDEX idx_student_info_registration ON student_info(registration_number);
CREATE INDEX idx_student_info_institution ON student_info(institution_id);
CREATE INDEX idx_student_info_birth_date ON student_info(birth_date);
