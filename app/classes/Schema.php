<?php

declare(strict_types=1);

namespace App\classes;

class Schema
{
    private string $tableName;
    private array $columns = [];

    public function createTable(string $tableName): self
    {
        $this->tableName = $tableName;
        $this->columns = [];
        return $this;
    }

    public function addColumn(string $name, string $type, array $options = []): self
    {
        $this->columns[] = [
            'name' => $name,
            'type' => $type,
            'options' => $options,
        ];
        return $this;
    }

    public function buildCreateTableSQL(): string
    {
        $columnsSQL = array_map(function ($column) {
            $optionsSQL = implode(' ', $column['options']);
            return "`{$column['name']}` {$column['type']} {$optionsSQL}";
        }, $this->columns);

        $columnsSQLString = implode(",\n", $columnsSQL);

        return "CREATE TABLE IF NOT EXISTS `{$this->tableName}` (\n{$columnsSQLString}\n);";
    }

    public function dropTable(string $tableName): string
    {
        return "DROP TABLE IF EXISTS `{$tableName}`;";
    }
}
