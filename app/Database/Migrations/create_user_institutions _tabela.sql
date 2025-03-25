-- Cria a tabela de relacionamento entre usuários e instituições
CREATE TABLE IF NOT EXISTS user_institutions (
    id INT NOT NULL AUTO_INCREMENT,
    user_id INT NOT NULL,
    institution_id INT NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_institution (user_id, institution_id),
    CONSTRAINT user_institutions_user_fk FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT user_institutions_institution_fk FOREIGN KEY (institution_id) REFERENCES institutions(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Adiciona índices para consultas comuns
CREATE INDEX idx_user_institutions_user ON user_institutions(user_id);
CREATE INDEX idx_user_institutions_institution ON user_institutions(institution_id);
CREATE INDEX idx_user_institutions_primary ON user_institutions(is_primary);

-- Script para migrar os dados existentes
-- Copia os vínculos atuais da tabela users para a nova tabela
INSERT INTO user_institutions (user_id, institution_id, is_primary)
SELECT id, institution_id, TRUE FROM users 
WHERE institution_id IS NOT NULL AND deleted_at IS NULL;

-- Copia os vínculos de guardians_students para os responsáveis
-- que têm múltiplas instituições através dos alunos
INSERT INTO user_institutions (user_id, institution_id, is_primary)
SELECT DISTINCT gs.guardian_user_id, gs.institution_id, FALSE
FROM guardians_students gs
LEFT JOIN user_institutions ui ON ui.user_id = gs.guardian_user_id AND ui.institution_id = gs.institution_id
WHERE gs.guardian_user_id IS NOT NULL AND gs.institution_id IS NOT NULL
AND ui.id IS NULL; -- Não insere relacionamentos que já existem

-- Atualiza is_primary para TRUE apenas para o primeiro registro de cada usuário
-- se nenhum registro foi marcado como primário
UPDATE user_institutions ui1
JOIN (
    SELECT user_id, MIN(id) as min_id
    FROM user_institutions
    WHERE is_primary = FALSE
    GROUP BY user_id
    HAVING COUNT(CASE WHEN is_primary = TRUE THEN 1 ELSE NULL END) = 0
) ui2 ON ui1.user_id = ui2.user_id AND ui1.id = ui2.min_id
SET ui1.is_primary = TRUE;

-- Opcional: Adicionar uma constraint para garantir que cada usuário tenha pelo menos
-- uma instituição marcada como primária
-- ALTER TABLE user_institutions ADD CONSTRAINT chk_primary_institution CHECK (
--    EXISTS (SELECT 1 FROM user_institutions ui2 WHERE ui2.user_id = user_id AND ui2.is_primary = TRUE)
-- );