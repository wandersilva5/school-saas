-- Tabela de registros de entrada e saída
CREATE TABLE access_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    person_id INT NOT NULL,
    institution_id INT NOT NULL,
    type ENUM('entrada', 'saída') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('autorizado', 'regular', 'visitante') NOT NULL,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);

-- Tabela de pessoas (funcionários, alunos, etc)
CREATE TABLE people (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    document VARCHAR(20),
    type ENUM('funcionario', 'aluno', 'visitante', 'prestador') NOT NULL,
    institution_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);

-- Tabela de autorizações pendentes
CREATE TABLE pending_authorizations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    person_name VARCHAR(100) NOT NULL,
    person_type ENUM('visitante', 'prestador') NOT NULL,
    reason TEXT NOT NULL,
    requested_by VARCHAR(100) NOT NULL,
    institution_id INT NOT NULL,
    status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);

-- Tabela de alertas
CREATE TABLE alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    institution_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('ativo', 'resolvido') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (institution_id) REFERENCES institutions(id)
);
