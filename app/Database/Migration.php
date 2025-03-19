<?php

namespace App\Database;

use App\Config\Database;

class Migration
{
    private $db;
    private $pdo;
    private $migrationsTable = 'migrations';

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->pdo = $this->db->getConnection();
        $this->createMigrationsTable();
    }

    /**
     * Cria a tabela de migrations se ela nu00e3o existir
     */
    private function createMigrationsTable()
    {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );");
    }

    /**
     * Verifica se uma migration ju00e1 foi executada
     */
    private function isMigrationExecuted($migration)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->migrationsTable} WHERE migration = ?");
        $stmt->execute([$migration]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Registra a migration como executada
     */
    private function markMigrationAsExecuted($migration)
    {
        $stmt = $this->pdo->prepare("INSERT INTO {$this->migrationsTable} (migration) VALUES (?)");
        $stmt->execute([$migration]);
    }

    /**
     * Execute todas as migrations
     */
    public function migrate()
    {
        $migrationsDir = __DIR__ . '/Migrations';
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }

        $migrations = glob($migrationsDir . '/*.php');
        if (empty($migrations)) {
            echo "Nenhuma migration encontrada.\n";
            return;
        }

        // Ordena as migrations pelo nome do arquivo
        sort($migrations);

        foreach ($migrations as $migration) {
            $migrationName = basename($migration);
            if (!$this->isMigrationExecuted($migrationName)) {
                require_once $migration;
                $className = 'App\\Database\\Migrations\\' . pathinfo($migrationName, PATHINFO_FILENAME);
                $instance = new $className();
                
                echo "Executando migration: {$migrationName}\n";
                
                try {
                    $this->pdo->beginTransaction();
                    $instance->up($this->pdo);
                    $this->markMigrationAsExecuted($migrationName);
                    $this->pdo->commit();
                    echo "Migration {$migrationName} executada com sucesso.\n";
                } catch (\Exception $e) {
                    $this->pdo->rollBack();
                    echo "Erro ao executar migration {$migrationName}: {$e->getMessage()}\n";
                    throw $e;
                }
            } else {
                echo "Migration {$migrationName} ju00e1 foi executada anteriormente.\n";
            }
        }
        
        echo "Processo de migrations concluu00eddo.\n";
    }

    /**
     * Cria uma nova migration
     */
    public function create($name)
    {
        $migrationsDir = __DIR__ . '/Migrations';
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $className = ucfirst($name);
        $filename = "{$timestamp}_{$name}.php";
        $filePath = $migrationsDir . '/' . $filename;

        $template = <<<PHP
<?php

namespace App\Database\Migrations;

class {$className}
{
    public function up(\PDO $pdo)
    {
        // Coloque seu cu00f3digo SQL aqui
        $sql = ""; // Exemplo: CREATE TABLE users (...)
        $pdo->exec($sql);
    }

    public function down(\PDO $pdo)
    {
        // Coloque seu cu00f3digo SQL para reverter as alterau00e7u00f5es aqui
        $sql = ""; // Exemplo: DROP TABLE users
        $pdo->exec($sql);
    }
}
PHP;

        if (file_put_contents($filePath, $template)) {
            echo "Migration {$filename} criada com sucesso.\n";
            return true;
        } else {
            echo "Erro ao criar migration {$filename}.\n";
            return false;
        }
    }

    /**
     * Recriar todas as tabelas (resetar o banco de dados)
     */
    public function reset()
    {
        // Confirmau00e7u00e3o de seguranu00e7a
        echo "ATENu00c7u00c3O: Esta au00e7u00e3o iru00e1 APAGAR TODAS as tabelas do banco de dados e recriu00e1-las.\n";

        try {
            // Desativa foreign key checks para permitir a exclusu00e3o de tabelas com chaves estrangeiras
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

            // Lista todas as tabelas
            $stmt = $this->pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($tables as $table) {
                echo "Apagando tabela: {$table}\n";
                $this->pdo->exec("DROP TABLE IF EXISTS {$table}");
            }

            // Reativa foreign key checks
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            echo "Todas as tabelas foram apagadas.\n";

            // Recria a tabela de migrations e executa todas as migrations
            $this->createMigrationsTable();
            $this->migrate();

            return true;
        } catch (\Exception $e) {
            echo "Erro ao resetar o banco de dados: {$e->getMessage()}\n";
            return false;
        }
    }
}
