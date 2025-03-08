<?php

namespace App\Config;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        // Em ambiente Docker, o host deve ser o nome do serviço, não 'localhost'
        $host = 'mysql';
        $dbname = getenv('DB_NAME') ?: 'app_db'; // Alterado para o nome definido no docker-compose.yml
        $username = getenv('DB_USER') ?: 'app_user'; // Alterado para o usuário definido no docker-compose.yml
        $password = getenv('DB_PASS') ?: 'app_pass'; // Alterado para a senha definida no docker-compose.yml

        try {
            $this->connection = new \PDO(
                "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (\PDOException $e) {
            die("Conexão falhou: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}