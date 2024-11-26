<?php

declare(strict_types=1);

namespace App\classes;

use PDO;
use PDOException;

class MigrationManager
{
    private array $migrationFiles;

    public function __construct(private PDO $pdo, array $migrationFiles)
    {
        $this->migrationFiles = $migrationFiles;
    }
    private function getMigrations(): array
    {
        $migrations = [];
        foreach ($this->migrationFiles as $file) {
            $migrationClass = $this->getMigrationClassFromFile($file);
            $migrations[] = $migrationClass;
        }

        return $migrations;
    }

    private function getPendingMigrations(): array
    {
        $executedMigrations = $this->getExecutedMigrations();
        $allMigrations = $this->getMigrations();

        return array_diff($allMigrations, $executedMigrations);
    }

    public function getExecutedMigrations(): array
    {
        try {
            $stmt = $this->pdo->query("SELECT name FROM migrations");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error fetching executed migrations: " . $e->getMessage());
            return [];
        }
    }

    private function runMigration(string $migration): void
    {
        $migrationInstance = new $migration($this->pdo);
        $migrationInstance->up();

        $stmt = $this->pdo->prepare("INSERT INTO migrations (name, executed_at) VALUES (?, ?)");
        $stmt->execute([$migration, date('Y-m-d H:i:s')]);

        echo "Migration $migration executed.\n";
    }

    private function rollbackMigration(string $migration): void
    {
        $migrationInstance = new $migration($this->pdo);
        $migrationInstance->down();

        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE name = ?");
        $stmt->execute([$migration]);

        echo "Migration $migration rolled back.\n";
    }

    public function migrateUp(): void
    {
        $pendingMigrations = $this->getPendingMigrations();

        foreach ($pendingMigrations as $migration) {
            $this->runMigration($migration);
        }
    }

    public function migrateDown(): void
    {
        $executedMigrations = $this->getExecutedMigrations();

        if (count($executedMigrations) <= 0) {
            echo "No migrations to roll back.\n";
            return;
        }
        $lastMigration = array_pop($executedMigrations);
        $this->rollbackMigration($lastMigration);
    }

    public function getMigrationClassFromFile(string $file): string
    {
        $filename = basename($file, '.php');
        $class = 'App\migrations\\' . $filename;

        require_once $file;

        return $class;
    }
}
