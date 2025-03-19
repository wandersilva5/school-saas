<?php

namespace App\Database\Migrations;

class Institutions
{
    public function up(\PDO $pdo)
    {
        $sql = "CREATE TABLE IF NOT EXISTS institutions (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(100)  NOT NULL,
            `domain` varchar(100)  NOT NULL,
            `logo_url` varchar(255)  DEFAULT NULL,
            `email` varchar(255)  NOT NULL,
            `phone` varchar(20)  NOT NULL,
            `name_contact` varchar(100)  NOT NULL,
            `active` tinyint(1) DEFAULT '1',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $pdo->exec($sql);
    }

    public function down(\PDO $pdo)
    {
        $sql = "DROP TABLE IF EXISTS institutions;";
        $pdo->exec($sql);
    }
}
