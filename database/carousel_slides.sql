-- Script para criar a tabela de slides do carrossel
CREATE TABLE IF NOT EXISTS carousel_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url VARCHAR(255) NOT NULL,
    institution_id INT NOT NULL,
    order_num INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
);

-- Dados iniciais
INSERT INTO carousel_slides (image_url, institution_id, order_num) VALUES
('https://img.freepik.com/fotos-gratis/menino-de-copia-espaco-com-livros-mostrando-sinal-ok_23-2148469950.jpg', 1, 1),
('https://img.freepik.com/fotos-gratis/alunos-sabendo-a-resposta-certa_329181-14271.jpg', 1, 2),
('https://img.freepik.com/fotos-gratis/estudante-feliz-com-sua-mochila-e-livros_1098-3454.jpg', 1, 3),
('https://img.freepik.com/fotos-gratis/livro-com-fundo-de-placa-verde_1150-3837.jpg', 1, 4);
