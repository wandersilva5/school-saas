CREATE TABLE IF NOT EXISTS announcements (
    id INT NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Título do comunicado',
    content TEXT COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Conteúdo do comunicado',
    institution_id INT NOT NULL COMMENT 'ID da instituição',
    author_id INT NOT NULL COMMENT 'ID do usuário que criou',
    start_date DATE DEFAULT NULL COMMENT 'Data de início da exibição',
    end_date DATE DEFAULT NULL COMMENT 'Data final da exibição',
    priority ENUM('Baixa', 'Normal', 'Alta', 'Urgente') DEFAULT 'Normal' COMMENT 'Prioridade do comunicado',
    active TINYINT(1) DEFAULT 1 COMMENT 'Status de ativação',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT announcements_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id),
    CONSTRAINT announcements_author_fk FOREIGN KEY (author_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para otimização
CREATE INDEX idx_announcements_institution ON announcements(institution_id);
CREATE INDEX idx_announcements_dates ON announcements(start_date, end_date);
CREATE INDEX idx_announcements_priority ON announcements(priority);
CREATE INDEX idx_announcements_active ON announcements(active);
