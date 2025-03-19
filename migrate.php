<?php

// Script de migração simplificado para recriar tabelas

// Conectar ao banco de dados diretamente sem depender de classes com autoloading
$host = 'mysql';
$dbname = 'app_db';
$username = 'app_user';
$password = 'app_pass';

try {
    // Conectar ao servidor MySQL sem especificar um banco de dados
    $pdo = new PDO("mysql:host={$host}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Conectado ao servidor MySQL com sucesso.\n";

    // Verificar se o banco de dados existe, se não existir, criar
    $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$dbname}'");
    if (!$stmt->fetch()) {
        echo "Criando banco de dados '{$dbname}'...\n";
        $pdo->exec("CREATE DATABASE {$dbname} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    // Conectar ao banco de dados específico
    $pdo = new PDO("mysql:host={$host};dbname={$dbname}", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Conectado ao banco de dados '{$dbname}' com sucesso.\n";

    // Desativar verificação de chaves estrangeiras para permitir a exclusão de tabelas
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');

    // Listar todas as tabelas e excluí-las
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "Excluindo tabela {$table}...\n";
        $pdo->exec("DROP TABLE {$table}");
    }

    echo "Todas as tabelas foram excluídas.\n";
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    // Criar tabela de instituições primeiro
    echo "Criando tabela 'institutions'...\n";
    $pdo->exec('CREATE TABLE institutions (
    `id` int NOT NULL AUTO_INCREMENT,
        `name` varchar(100)  NOT NULL,
        `domain` varchar(100)  NOT NULL,
        `logo_url` varchar(255)  DEFAULT NULL,
        `email` varchar(255)  NOT NULL,
        `phone` varchar(20)  NOT NULL,
        `name_contact` varchar(100)  NOT NULL,
        `active` tinyint(1) DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // Inserir uma instituição
    echo "Inserindo instituição padrão...\n";
    $stmt = $pdo->prepare("INSERT INTO institutions (name, domain, email, phone, name_contact, active) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Instituição Demo', 'demo.com', 'contato@demo.com', '(11) 99999-9999', 'Administrador', 1]);
    echo "Instituição padrão criada com ID 1\n";

    // Criar tabela de usuários depois da tabela institutions
    echo "Criando tabela 'users'...\n";
    $pdo->exec('CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            institution_id INT NOT NULL,
            active tinyint(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            KEY `institution_id` (`institution_id`),        
            CONSTRAINT `users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // Inserir um usuário administrador padrão
    $adminName = 'Admin';
    $adminEmail = 'admin@example.com';
    $adminPassword = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, institution_id) VALUES (?, ?, ?, 1)");
    $stmt->execute([$adminName, $adminEmail, $adminPassword]);
    echo "Usuário administrador criado (email: {$adminEmail}, senha: 123456)\n";

    // Criar tabela de roles
    echo "Criando tabela 'roles'...\n";
    $pdo->exec('CREATE TABLE roles (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL,
    `description` text,
    `institution_id` int NOT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `institution_id` (`institution_id`),
    CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // Criar tabela de permissions
    echo "Criando tabela 'permissions'...\n";
    $pdo->exec('CREATE TABLE permissions (
`id` int NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL,
`description` text,
`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // Criar tabela de role_permissions
    echo "Criando tabela 'role_permissions'...\n";
    $pdo->exec('CREATE TABLE role_permissions (
`role_id` int NOT NULL,
`permission_id` int NOT NULL,
`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`role_id`,`permission_id`),
KEY `permission_id` (`permission_id`),
CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

// Criar tabela de user_roles
echo "Criando tabela 'user_roles'...\n";
$pdo->exec('CREATE TABLE user_roles (
`user_id` int NOT NULL,
`role_id` int NOT NULL,
`created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`user_id`,`role_id`),
KEY `role_id` (`role_id`),
CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // Criar tabela de responsáveis
    echo "Criando tabela 'responsaveis'...\n";
    $pdo->exec('CREATE TABLE responsaveis (
`id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telefone` varchar(20)  NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `institution_id` int NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_responsaveis_institution` (`institution_id`),
  CONSTRAINT `responsaveis_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    // Criar tabela de alunos
    echo "Criando tabela 'alunos'...\n";
    $pdo->exec('CREATE TABLE alunos (
`id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `data_nascimento` date NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `responsavel_id` int NOT NULL,
  `institution_id` int NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_alunos_responsavel` (`responsavel_id`),
  KEY `idx_alunos_institution` (`institution_id`),
  CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`responsavel_id`) REFERENCES `responsaveis` (`id`),
  CONSTRAINT `alunos_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');


// Criar tabela de slides_home
echo "Criando tabela 'slides_home'...\n";
$pdo->exec('CREATE TABLE slides_home (
`id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` text NOT NULL,
  `imagem_url` varchar(255) NOT NULL,
  `ordem` int NOT NULL DEFAULT 0,
  `link` varchar(255) DEFAULT NULL,
  `texto_botao` varchar(50) DEFAULT NULL,
  `institution_id` int NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_slides_institution` (`institution_id`),
  CONSTRAINT `fk_slides_institution` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

    echo "\nTodas as tabelas foram criadas com sucesso!\n";
} catch (PDOException $e) {
    die('Erro de banco de dados: ' . $e->getMessage() . "\n");
}
