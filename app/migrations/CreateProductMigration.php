<?php

declare(strict_types=1);

namespace App\migrations;

use App\classes\Migration;
use PDO;

class CreateProductMigration extends Migration
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            cost INT NOT NULL
        )";

        $this->pdo->exec($sql);
    }

    public function down(): void
    {
        $sql = "DROP TABLE IF EXISTS products";
        $this->pdo->exec($sql);
    }
}
