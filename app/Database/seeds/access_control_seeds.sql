-- Limpar dados existentes
TRUNCATE TABLE access_logs;
TRUNCATE TABLE people;

-- Inserindo pessoas de teste para a instituição específica
INSERT INTO people (name, document, type, institution_id) VALUES
('João Silva', '123.456.789-00', 'funcionario', 2),
('Maria Santos', '987.654.321-00', 'funcionario', 2),
('Pedro Oliveira', '111.222.333-44', 'prestador', 2),
('Ana Costa', '444.555.666-77', 'funcionario', 2),
('Lucas Mendes', '777.888.999-00', 'visitante', 2),
('Julia Pereira', '222.333.444-55', 'prestador', 2),
('Carlos Eduardo', '555.666.777-88', 'visitante', 2),
('Amanda Lima', '888.999.000-11', 'funcionario', 2),
('Roberto Alves', '333.444.555-66', 'visitante', 2),
('Fernanda Santos', '666.777.888-99', 'funcionario', 2);

-- Inserindo registros de acesso mais recentes
INSERT INTO access_logs (person_id, institution_id, type, status, created_at) VALUES
(1, 3, 'entrada', 'regular', NOW()),
(2, 3, 'entrada', 'regular', NOW() - INTERVAL 10 MINUTE),
(3, 3, 'entrada', 'autorizado', NOW() - INTERVAL 20 MINUTE),
(4, 3, 'saída', 'regular', NOW() - INTERVAL 30 MINUTE),
(5, 3, 'entrada', 'visitante', NOW() - INTERVAL 40 MINUTE),
(1, 3, 'saída', 'regular', NOW() - INTERVAL 50 MINUTE),
(6, 3, 'entrada', 'autorizado', NOW() - INTERVAL 60 MINUTE);

-- Inserindo autorizações pendentes
INSERT INTO pending_authorizations (person_name, person_type, reason, requested_by, institution_id, created_at) VALUES
('Carlos Eduardo', 'visitante', 'Reunião com Diretoria', 'Dept. RH', 1, NOW() - INTERVAL 1 HOUR),
('Julia Mendes', 'prestador', 'Manutenção do Ar Condicionado', 'Dept. Manutenção', 1, NOW() - INTERVAL 45 MINUTE),
('Roberto Alves', 'visitante', 'Entrega de Documentos', 'Recepção', 1, NOW() - INTERVAL 30 MINUTE),
('Patricia Santos', 'prestador', 'Serviço de Pintura', 'Dept. Manutenção', 1, NOW() - INTERVAL 15 MINUTE),
('Miguel Costa', 'visitante', 'Visita Técnica', 'Dept. TI', 1, NOW());

-- Inserindo alertas
INSERT INTO alerts (institution_id, type, message, status, created_at) VALUES
(1, 'Segurança', 'Porta principal com defeito no sensor', 'ativo', NOW() - INTERVAL 2 HOUR),
(1, 'Acesso', 'Tentativa de acesso não autorizado - Portão 2', 'ativo', NOW() - INTERVAL 1 HOUR),
(1, 'Sistema', 'Atualização de segurança pendente', 'ativo', NOW()),
(1, 'Segurança', 'Câmera 3 offline', 'resolvido', NOW() - INTERVAL 3 HOUR),
(1, 'Acesso', 'Cartão de acesso bloqueado - ID 1234', 'resolvido', NOW() - INTERVAL 4 HOUR);




