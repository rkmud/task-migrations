<?php

declare(strict_types=1);

namespace App\classes;

use PDO;

final class MigrationManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getMigrations(): array
    {
        $migrations = [];
        $migrationsFolder = str_replace('\\', DIRECTORY_SEPARATOR, realpath(dirname(__DIR__)));
        $files = glob($migrationsFolder . '/migrations/*.php');

        foreach ($files as $file) {
            $migrationClass = $this->getMigrationClassFromFile($file);
            $migrations[] = $migrationClass;
        }
        return $migrations;
    }

    public function getPendingMigrations(): array
    {
        $executedMigrations = $this->getExecutedMigrations();
        $allMigrations = $this->getMigrations();

        return array_diff($allMigrations, $executedMigrations);
    }

    public function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT name FROM migrations");

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function runMigration(string $migration): void
    {
        $migrationInstance = new $migration($this->pdo);
        $migrationInstance->up();

        $stmt = $this->pdo->prepare("INSERT INTO migrations (name, executed_at) VALUES (?, ?)");
        $stmt->execute([$migration, date('Y-m-d H:i:s')]);

        echo "Migration $migration executed.\n";
    }

    public function rollbackMigration(string $migration): void
    {
        $migrationInstance = new $migration($this->pdo);
        $migrationInstance->down();

        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE name = ?");
        $stmt->execute([$migration]);

        echo "Migration $migration rolled back.\n";
    }

    private function getMigrationClassFromFile(string $file): string
    {
        $filename = basename($file, '.php');
        $class = 'App\migrations\\' . $filename;

        require_once $file;

        return $class;
    }
}
