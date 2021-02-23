<?php
declare(strict_types=1);

namespace papi\Migrations;

use JetBrains\PhpStorm\Pure;
use papi\Migrations\Schema\SchemaDiffGenerator;

class MigrationQueryBuilder
{
    private SchemaDiffGenerator $diffGenerator;

    public function __construct()
    {
        $this->diffGenerator = new SchemaDiffGenerator();
    }

    public function getCurrentMappingArray(): ?array
    {
        return $this->diffGenerator?->getCurrentMapping()?->toArray();
    }

    public function getSqlStatements(): array
    {
        $statements = [];

        foreach ($this->diffGenerator->indexesToRemove as $key) {
            $statements[] = $this->dropIndex($key);
        }
        foreach ($this->diffGenerator->foreignKeysToRemove as $table => $keyNames) {
            foreach ($keyNames as $name => $options) {
                $statements[] = $this->dropFK($table, $name);
            }
        }

        foreach ($this->diffGenerator->columnsToRemove as $table => $columns) {
            foreach ($columns as $column) {
                $statements[] = $this->dropColumn($table, $column);
            }
        }

        foreach ($this->diffGenerator->tablesToRemove as $table) {
            $statements[] = $this->dropTable($table);
        }

        foreach ($this->diffGenerator->tablesToCreate as $table => $fields) {
            $statements[] = $this->createTable($table, $fields);
        }

        foreach ($this->diffGenerator->columnsToCreate as $table => $columns) {
            foreach ($columns as $column => $options) {
                $statements[] = $this->createColumn($table, $column, $options);
            }
        }

        foreach ($this->diffGenerator->columnsToChange as $table => $columns) {
            foreach ($columns as $column => $options) {
                $statements[] = $this->alterColumn($table, $column, $options);
            }
        }

        foreach ($this->diffGenerator->foreignKeysToCreate as $table => $keys) {
            foreach ($keys as $name => $options) {
                $statements[] = $this->createFK($table, $name, $options);
            }
        }

        foreach ($this->diffGenerator->indexesToCreate as $key) {
            $statements[] = $this->createIndex($key);
        }

        return $statements;
    }

    #[Pure] private function createTable(
        string $name,
        array $fields
    ): string {
        $fieldsString = '';
        $lastField = array_key_last($fields);

        foreach ($fields as $field => $options) {
            $fieldsString .= "$field $options";
            if ($field !== $lastField) {
                $fieldsString .= ', ';
            }
        }

        return "create table $name($fieldsString)";
    }

    private function dropTable(
        string $name
    ): string {
        return "drop table $name";
    }

    private function createColumn(
        string $table,
        string $name,
        string $options
    ): string {
        return "alter table $table add column $name $options";
    }

    private function dropColumn(
        string $table,
        string $name
    ): string {
        return "alter table $table drop column $name";
    }

    private function alterColumn(
        string $table,
        string $name,
        string $options
    ): string {
        return "alter table $table alter column $name type $options";
    }

    private function createFK(
        string $table,
        string $name,
        string $options
    ): string {
        return "alter table $table add constraint $table".'_'.$name."_fkey foreign key ($name) $options";
    }

    private function dropFK(
        string $table,
        string $name
    ): string {
        return "alter table $table drop constraint $table".'_'.$name."_fkey";
    }

    private function createIndex(
        string $definition
    ): string {
        return "create $definition";
    }

    private function dropIndex(
        string $definition
    ): string {
        $definition = str_ireplace('UNIQUE ', '', $definition);
        $definitionArray = explode(' ', $definition);

        return "drop index $definitionArray[1]";
    }
}