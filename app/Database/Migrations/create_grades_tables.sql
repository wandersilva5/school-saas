-- Tabela de Disciplinas
CREATE TABLE IF NOT EXISTS subjects (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da disciplina',
    code VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT 'Código da disciplina',
    description TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Descrição da disciplina',
    institution_id INT NOT NULL,
    workload INT COMMENT 'Carga horária em horas',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_subject_code (code, institution_id),
    CONSTRAINT subjects_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Notas
CREATE TABLE IF NOT EXISTS grades (
    id INT NOT NULL AUTO_INCREMENT,
    student_id INT NOT NULL COMMENT 'ID do aluno',
    subject_id INT NOT NULL COMMENT 'ID da disciplina',
    class_id INT NOT NULL COMMENT 'ID da turma',
    grade DECIMAL(5,2) NOT NULL COMMENT 'Nota do aluno',
    evaluation_type ENUM('Prova', 'Trabalho', 'Exercício', 'Projeto', 'Outros') NOT NULL DEFAULT 'Prova',
    evaluation_date DATE NOT NULL COMMENT 'Data da avaliação',
    period VARCHAR(20) COMMENT 'Período/Bimestre',
    weight DECIMAL(3,1) DEFAULT 1.0 COMMENT 'Peso da nota',
    observation TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Observações',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT grades_student_fk FOREIGN KEY (student_id) REFERENCES users(id),
    CONSTRAINT grades_subject_fk FOREIGN KEY (subject_id) REFERENCES subjects(id),
    CONSTRAINT grades_class_fk FOREIGN KEY (class_id) REFERENCES classes(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para otimização
CREATE INDEX idx_subjects_institution ON subjects(institution_id);
CREATE INDEX idx_grades_student ON grades(student_id);
CREATE INDEX idx_grades_subject ON grades(subject_id);
CREATE INDEX idx_grades_class ON grades(class_id);
CREATE INDEX idx_grades_date ON grades(evaluation_date);
