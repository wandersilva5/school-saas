<?php

namespace App\Database\Migrations;

class Users
{
    public function up(\PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            institution_id INT NOT NULL,
            active tinyint(1) DEFAULT '1',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `institution_id` (`institution_id`),        
            CONSTRAINT `users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
        
        // Inserir um usuário administrador padrão
        $adminName = 'Wander Silva';
        $adminEmail = 'admin@email.com';
        $adminPassword = password_hash('123456', PASSWORD_DEFAULT);
        
        $insertSql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')";
        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([$adminName, $adminEmail, $adminPassword]);
    }

    public function down(\PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS users;";
        $pdo->exec($sql);
    }
}
