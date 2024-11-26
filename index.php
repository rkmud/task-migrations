<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use App\classes\MigrationManager;

$pdo = new PDO('mysql:host=127.0.0.1;dbname=db', 'root', 'root');
$migrationsFolder = sprintf('%s%s', str_replace(
    '//',
    DIRECTORY_SEPARATOR,
    realpath(dirname(__FILE__))
), '/app/migrations/*.php');
$files = glob($migrationsFolder);
$manager = new MigrationManager($pdo, $files);
