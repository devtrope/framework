<?php

namespace Ludens\Database;

use PDO;

class Model
{
    private PDO $database;
    private string $table;
    protected array $attributes = [];

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
        $sql = $this->database->query("SELECT * FROM {$this->table}");
        $data = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => $this->hydrate($row), $data);
    }

    protected function hydrate(array $data)
    {
        $instance = new static();
        $instance->attributes = $data;
        return $instance;
    }
}
