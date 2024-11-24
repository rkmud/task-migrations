<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use App\classes\MigrationManager;

$pdo = new PDO('mysql:host=127.0.0.1;dbname=db', 'root', 'root');

$manager = new MigrationManager($pdo);

$command = $argv[1] ?? null;

switch ($command) {
    case 'migrate:up':
        $pendingMigrations = $manager->getPendingMigrations();

        foreach ($pendingMigrations as $migration) {
            $manager->runMigration($migration);
        }

        break;

    case 'migrate:down':
        $executedMigrations = $manager->getExecutedMigrations();

        if (count($executedMigrations) <= 0) {
            echo "No migrations to roll back.\n";

            return;
        }
        $lastMigration = array_pop($executedMigrations);
        $manager->rollbackMigration($lastMigration);
        break;

    default:
        echo "Invalid command.\n";
        break;
}
