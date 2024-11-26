<?php

declare(strict_types=1);

namespace App\migrations;

use App\classes\Migration;
use App\classes\Schema;

class CreateProductMigration extends Migration
{
    public function up(): void
    {
        $schema = new Schema();

        $sql = $schema->createTable('products')
            ->addColumn('id', 'INT AUTO_INCREMENT', ['PRIMARY KEY'])
            ->addColumn('name', 'VARCHAR(255)', ['NOT NULL'])
            ->addColumn('price', 'DECIMAL(10, 2)', ['NOT NULL'])
            ->buildCreateTableSQL();

        $this->getPdo()->exec($sql);
    }

    public function down(): void
    {
        $schema = new Schema();
        $sql = $schema->dropTable('products');
        $this->getPdo()->exec($sql);
    }
}
