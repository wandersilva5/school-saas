-- Tabela de Disciplinas
CREATE TABLE IF NOT EXISTS subjects (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da disciplina',
    code VARCHAR(20) COLLATE utf8mb4_unicode_ci COMMENT 'Código da disciplina',
    workload INT COMMENT 'Carga horária em horas',
    description TEXT COLLATE utf8mb4_unicode_ci COMMENT 'Descrição da disciplina',
    institution_id INT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_subject_institution (name, institution_id),
    CONSTRAINT subjects_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Inserir algumas disciplinas básicas
INSERT INTO subjects (name, code, institution_id) 
SELECT 'Matemática', 'MAT', i.id FROM institutions i
UNION ALL
SELECT 'Português', 'PORT', i.id FROM institutions i
UNION ALL
SELECT 'Ciências', 'CIE', i.id FROM institutions i
UNION ALL
SELECT 'História', 'HIST', i.id FROM institutions i
UNION ALL
SELECT 'Geografia', 'GEO', i.id FROM institutions i
UNION ALL
SELECT 'Educação Física', 'EDF', i.id FROM institutions i
UNION ALL
SELECT 'Artes', 'ART', i.id FROM institutions i
UNION ALL
SELECT 'Inglês', 'ING', i.id FROM institutions i;

-- Índices para otimização
CREATE INDEX idx_subjects_institution ON subjects(institution_id);
CREATE INDEX idx_subjects_code ON subjects(code);
