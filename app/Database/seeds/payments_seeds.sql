-- Inserir 20 registros de teste para a tabela de pagamentos
-- Assumindo que existem IDs válidos nas tabelas users e institutions

-- Definir IDs de exemplo para referência (ajuste conforme seus dados reais)
-- Substitua os valores de student_id e institution_id pelos IDs reais do seu banco

INSERT INTO payments 
(student_id, amount, description, due_date, reference_month, reference_year, status, 
boleto_code, boleto_url, discount_amount, fine_amount, payment_date, payment_method, 
payment_amount, notes, institution_id) 
VALUES
-- Janeiro 2024
(1, 500.00, 'Mensalidade Escolar - Janeiro', '2024-01-10', 1, 2024, 'Pago', 
'34191790010104351004791020150008885000000050000', 'https://example.com/boleto/12345', 0.00, 0.00, 
'2024-01-08', 'PIX', 500.00, 'Pagamento realizado antecipadamente', 1),

(2, 500.00, 'Mensalidade Escolar - Janeiro', '2024-01-10', 1, 2024, 'Pago', 
'34191790010104351004791020150008885000000050001', 'https://example.com/boleto/12346', 50.00, 0.00, 
'2024-01-09', 'Boleto', 450.00, 'Desconto por antecipação', 1),

(3, 500.00, 'Mensalidade Escolar - Janeiro', '2024-01-10', 1, 2024, 'Atrasado', 
'34191790010104351004791020150008885000000050002', 'https://example.com/boleto/12347', 0.00, 25.00, 
NULL, NULL, NULL, 'Notificação enviada em 15/01/2024', 1),

-- Fevereiro 2024
(1, 500.00, 'Mensalidade Escolar - Fevereiro', '2024-02-10', 2, 2024, 'Pago', 
'34191790010104351004791020150008885000000050003', 'https://example.com/boleto/12348', 0.00, 0.00, 
'2024-02-10', 'Cartão', 500.00, 'Pagamento no prazo', 1),

(2, 500.00, 'Mensalidade Escolar - Fevereiro', '2024-02-10', 2, 2024, 'Pago', 
'34191790010104351004791020150008885000000050004', 'https://example.com/boleto/12349', 0.00, 0.00, 
'2024-02-09', 'Dinheiro', 500.00, 'Pagamento realizado na secretaria', 1),

(3, 500.00, 'Mensalidade Escolar - Fevereiro', '2024-02-10', 2, 2024, 'Pago', 
'34191790010104351004791020150008885000000050005', 'https://example.com/boleto/12350', 0.00, 35.00, 
'2024-02-25', 'PIX', 535.00, 'Pagamento com multa por atraso', 1),

-- Março 2024
(1, 500.00, 'Mensalidade Escolar - Março', '2024-03-10', 3, 2024, 'Pago', 
'34191790010104351004791020150008885000000050006', 'https://example.com/boleto/12351', 25.00, 0.00, 
'2024-03-05', 'PIX', 475.00, 'Desconto por bom desempenho acadêmico', 1),

(2, 500.00, 'Mensalidade Escolar - Março', '2024-03-10', 3, 2024, 'Cancelado', 
'34191790010104351004791020150008885000000050007', 'https://example.com/boleto/12352', 0.00, 0.00, 
NULL, NULL, NULL, 'Cancelado por emissão incorreta', 1),

(2, 500.00, 'Mensalidade Escolar - Março (reemissão)', '2024-03-15', 3, 2024, 'Pago', 
'34191790010104351004791020150008885000000050008', 'https://example.com/boleto/12353', 0.00, 0.00, 
'2024-03-14', 'Boleto', 500.00, 'Boleto reemitido após cancelamento', 1),

(3, 500.00, 'Mensalidade Escolar - Março', '2024-03-10', 3, 2024, 'Atrasado', 
'34191790010104351004791020150008885000000050009', 'https://example.com/boleto/12354', 0.00, 50.00, 
NULL, NULL, NULL, 'Segunda notificação enviada em 25/03/2024', 1),

-- Abril 2024
(1, 520.00, 'Mensalidade Escolar - Abril', '2024-04-10', 4, 2024, 'Pago', 
'34191790010104351004791020150008885000000052000', 'https://example.com/boleto/12355', 20.00, 0.00, 
'2024-04-08', 'PIX', 500.00, 'Desconto por pagamento antecipado', 1),

(2, 520.00, 'Mensalidade Escolar - Abril', '2024-04-10', 4, 2024, 'Pendente', 
'34191790010104351004791020150008885000000052001', 'https://example.com/boleto/12356', 0.00, 0.00, 
NULL, NULL, NULL, 'Primeira notificação enviada em 05/04/2024', 1),

(3, 520.00, 'Mensalidade Escolar - Abril', '2024-04-10', 4, 2024, 'Atrasado', 
'34191790010104351004791020150008885000000052002', 'https://example.com/boleto/12357', 0.00, 0.00, 
NULL, NULL, NULL, 'Aluno com dificuldades financeiras - entrar em contato', 1),

-- Maio 2024
(1, 520.00, 'Mensalidade Escolar - Maio', '2024-05-10', 5, 2024, 'Pendente', 
'34191790010104351004791020150008885000000052003', 'https://example.com/boleto/12358', 0.00, 0.00, 
NULL, NULL, NULL, NULL, 1),

(4, 520.00, 'Mensalidade Escolar - Maio', '2024-05-10', 5, 2024, 'Pendente', 
'34191790010104351004791020150008885000000052004', 'https://example.com/boleto/12359', 0.00, 0.00, 
NULL, NULL, NULL, 'Aluno novo - primeira mensalidade', 1),

-- Taxas especiais e outros pagamentos
(1, 150.00, 'Taxa de material didático', '2024-02-15', 2, 2024, 'Pago', 
'34191790010104351004791020150008885000000015000', 'https://example.com/boleto/12360', 0.00, 0.00, 
'2024-02-14', 'Cartão', 150.00, 'Material do primeiro semestre', 1),

(2, 150.00, 'Taxa de material didático', '2024-02-15', 2, 2024, 'Pago', 
'34191790010104351004791020150008885000000015001', 'https://example.com/boleto/12361', 0.00, 0.00, 
'2024-02-13', 'PIX', 150.00, 'Material do primeiro semestre', 1),

(3, 150.00, 'Taxa de material didático', '2024-02-15', 2, 2024, 'Atrasado', 
'34191790010104351004791020150008885000000015002', 'https://example.com/boleto/12362', 0.00, 15.00, 
NULL, NULL, NULL, 'Pendente pagamento com multa', 1),

(1, 300.00, 'Excursão escolar', '2024-03-20', 3, 2024, 'Pago', 
'34191790010104351004791020150008885000000030000', 'https://example.com/boleto/12363', 0.00, 0.00, 
'2024-03-15', 'PIX', 300.00, 'Excursão para o museu e parque', 1),

(2, 300.00, 'Excursão escolar', '2024-03-20', 3, 2024, 'Cancelado', 
'34191790010104351004791020150008885000000030001', 'https://example.com/boleto/12364', 0.00, 0.00, 
NULL, NULL, NULL, 'Aluno não participará da excursão', 1);