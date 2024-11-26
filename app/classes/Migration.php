<?php

declare(strict_types=1);

namespace App\classes;

use PDO;

abstract class Migration
{
    public function __construct(private PDO $pdo) {}

    protected function getPdo(): PDO
    {
        return $this->pdo;
    }
    abstract public function up();

    abstract public function down();
}
