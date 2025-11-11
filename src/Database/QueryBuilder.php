<?php

namespace Ludens\Database;

use PDO;

/**
 * Fluent SQL Query Builder.
 * 
 * @package Ludens\Database
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class QueryBuilder
{
    private PDO $connection;
    private string $table;

    private array $select = ['*'];
    private array $where = [];
    private array $bindings = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $joins = [];
    private ?string $groupBy = null;
    private array $having = [];

    /**
     * @param \PDO $connection
     * @param string $table
     */
    public function __construct(PDO $connection, string $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * Set columns to select.
     *
     * @param string|array<string> $columns
     * @return QueryBuilder
     */
    public function select(string|array $columns): self
    {
        $this->select = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    /**
     * Add a WHERE clause.
     *
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return QueryBuilder
     */
    public function where(string $column, mixed $operator, mixed $value = null): self
    {
        // If 2 arguments: where('name', 'John') -> where('name' '=', 'John') 
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $placeholder = $this->createPlaceholder($column);

        $this->where[] = "{$column} {$operator} {$placeholder}";
        $this->bindings[$placeholder] = $value;

        return $this;
    }

    /**
     * Add ORDER BY clause.
     *
     * @param string $column
     * @param string $suffix
     * @return QueryBuilder
     */
    public function orderBy(string $column, string $suffix = 'ASC'): self
    {
        $this->orderBy[] = "{$column} {$suffix}";
        return $this;
    }

    /**
     * Add LIMIT clause.
     *
     * @param int $limit
     * @return QueryBuilder
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Add OFFSET clause.
     *
     * @param int $offset
     * @return QueryBuilder
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get all results.
     *
     * @return array
     */
    public function get(): array
    {
        $sql = $this->toSQL();
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($this->bindings);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get first result.
     *
     * @return array|null
     */
    public function first(): array|null
    {
        $this->limit(1);
        $results = $this->get();

        return $results[0] ?? null;
    }

    /**
     * Get count of results.
     *
     * @return int
     */
    public function count(): int
    {
        $this->select = ['COUNT(*) as count'];
        $result = $this->first();

        return $result['count'] ?? 0;
    }

    /**
     * Check if results exist.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->count() > 0;
    }

    /**
     * Build the SQL query.
     *
     * @return string
     */
    public function toSQL(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->select);
        $sql .= ' FROM ' . $this->table;

        if (! empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }

        if (! empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    /**
     * Get bindings.
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->bindings;
    }

    /**
     * Create unique placeholder for binding.
     *
     * @param string $column
     * @return string
     */
    private function createPlaceholder(string $column): string
    {
        static $counter = 0;
        return ":{$column}_" . $counter++;
    }
}
