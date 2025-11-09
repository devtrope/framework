<?php

namespace Ludens\Database;

use ArrayAccess;
use PDO;

class Model implements ArrayAccess
{
    private PDO $database;
    private string $table;
    protected array $attributes = [];
    private string $primaryKey = 'id';

    public function __construct()
    {
        $this->database = ConnectionManager::getInstance()->getConnection();

        if (! isset($this->table)) {
            $this->table = $this->getTableName();
        }
    }

    public function query(): QueryBuilder
    {
        $instance = new static();
        return new QueryBuilder($instance->database, $instance->table);
    }

    public function all()
    {
        $data = $this->query()->get();
        return array_map(fn($row) => $this->hydrate($row), $data);
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

    public function find(int|string $id)
    {
        $stmt = $this->database->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        return $data ? $this->hydrate($data) : null;
    }

    public function where(array $conditions)
    {
        if (empty($conditions)) {
            return $this->all();
        }

        $whereClauses = [];
        $parameters = [];

        foreach ($conditions as $key => $value) {
            $whereClauses[] = "{$key} = :{$key}";
            $parameters[$key] = $value;
        }

        $whereString = implode(' AND ', $whereClauses);
        $query = "SELECT * FROM {$this->table} WHERE {$whereString}";

        $stmt = $this->database->prepare($query);
        $stmt->execute($parameters);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->hydrate($row), $data);
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

    public function save()
    {
        $setValues = [];
        $setKeys = [];
        $parameters = [];

        foreach ($this->attributes as $key => $value) {
            if ($key === $this->primaryKey) {
                continue;
            }

            $setValues[] = "{$key}";
            $setKeys[] = ":{$key}";
            $parameters[$key] = $value;
        }

        $setValuesString = implode(', ', $setValues);
        $setKeysString = implode(', ', $setKeys);
        $query = "INSERT INTO {$this->table} ({$setValuesString}) VALUES ({$setKeysString})";

        $stmt = $this->database->prepare($query);
        $stmt->execute($parameters);
    }

    protected function hydrate(array $data)
    {
        $instance = new static();
        $instance->attributes = $data;
        return $instance;
    }

    public function __isset(string $name)
    {
        if (isset($this->attributes[$name])) {
            return true;
        }

        if (isset($this->belongsTo[$name])) {
            return true;
        }

        if (isset($this->hasMany[$name])) {
            return true;
        }

        return false;
    }

    public function __get(string $name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if (isset($this->belongsTo[$name])) {
            $foreignKey = $this->belongsTo[$name]['foreign_key'];
            $modelClass = $this->belongsTo[$name]['model'];

            $relatedModel = new $modelClass();
            return $relatedModel->find($this->{$foreignKey});
        }

        if (isset($this->hasMany[$name])) {
            $foreignKey = $this->hasMany[$name]['foreign_key'];
            $modelClass = $this->hasMany[$name]['model'];

            $relatedModel = new $modelClass();
            return $relatedModel->where([$foreignKey => $this->id]);
        }

        return null;
    }

    public function __set(string $name, mixed $value)
    {
        $this->attributes[$name] = $value;
    }

    public function offsetExists($offset): bool
    {
        if (isset($this->attributes[$offset])) {
            return true;
        }

        if (isset($this->belongsTo[$offset])) {
            return true;
        }

        if (isset($this->hasMany[$offset])) {
            return true;
        }

        return false;
    }

    public function offsetGet($offset): mixed
    {
        if (array_key_exists($offset, $this->attributes)) {
            return $this->attributes[$offset];
        }

        if (isset($this->belongsTo[$offset])) {
            $foreignKey = $this->belongsTo[$offset]['foreign_key'];
            $modelClass = $this->belongsTo[$offset]['model'];

            $relatedModel = new $modelClass();
            return $relatedModel->find($this->{$foreignKey});
        }

        if (isset($this->hasMany[$offset])) {
            $foreignKey = $this->hasMany[$offset]['foreign_key'];
            $modelClass = $this->hasMany[$offset]['model'];

            $relatedModel = new $modelClass();
            return $relatedModel->where([$foreignKey => $this->id]);
        }

        return null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }
}
