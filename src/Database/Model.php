<?php

namespace Ludens\Database;

use PDO;

class Model
{
    private PDO $database;
    private string $table;
    protected array $attributes = [];
    private string $primaryKey = 'id';

    public function __construct()
    {
        $this->database = Database::getInstance()->connect();

        if (! isset($this->table)) {
            $this->table = $this->getTableName();
        }
    }

    protected function getTableName()
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $snakeCase = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className));
        return $this->pluralize($snakeCase);
    }

    protected function pluralize(string $word)
    {
        if (str_ends_with($word, 'y')) {
            return substr($word, 0, -1) . 'ies';
        }

        if (str_ends_with($word, 's') ||
        str_ends_with($word, 'x') ||
        str_ends_with($word, 'ch') ||
        str_ends_with($word, 'sh')) {
            return $word . 'es';
        }

        return $word . 's';
    }

    public function all()
    {
        $stmt = $this->database->query("SELECT * FROM {$this->table}");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => $this->hydrate($row), $data);
    }

    public function find(int|string $id)
    {
        $stmt = $this->database->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }

    protected function hydrate(array $data)
    {
        $instance = new static();
        $instance->attributes = $data;
        return $instance;
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value)
    {
        $this->attributes[$name] = $value;
    }

    public function update()
    {
        $id = $this->attributes[$this->primaryKey];

        $setClauses = [];
        $parameters = [];

        foreach ($this->attributes as $key => $value) {
            if ($key === $this->primaryKey) {
                continue;
            }

            $setClauses[] = "{$key} = :{$key}";
            $parameters[$key] = $value;
        }

        $parameters[$this->primaryKey] = $id;

        $setString = implode(', ', $setClauses);
        $query = "UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :{$this->primaryKey}";

        $stmt = $this->database->prepare($query);
        $stmt->execute($parameters);
    }
}
