<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use App\classes\MigrationManager;

$options = getopt('', ['direction:']);
$direction = $options['direction'] ?? null;
$pdo = new PDO('mysql:host=db;dbname=db', 'root', 'root');

$migrationsFolder = sprintf('%s%s', str_replace(
    '//',
    DIRECTORY_SEPARATOR,
    realpath(dirname(__FILE__))
), '/app/migrations/*.php');
$files = glob($migrationsFolder);
$manager = new MigrationManager($pdo, $files);

switch ($direction) {
    case 'up':
        $manager->migrateUp();
        break;

    case 'down':
        $manager->migrateDown();
        break;

    default:
        echo "Invalid command.\n";
        break;
}
