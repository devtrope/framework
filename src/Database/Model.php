<?php

namespace Ludens\Database;

use ArrayAccess;
use PDO;

/**
 * Handle the methods for the models.
 *
 * @package Ludens\Database
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 *
 * @implements ArrayAccess<string, string|null|array>
 */
class Model implements ArrayAccess
{
    private PDO $database;
    private string $table;
    protected array $attributes = [];
    private string $primaryKey = 'id';

    final public function __construct()
    {
        $this->database = ConnectionManager::getInstance()->getConnection();

        if (! isset($this->table)) {
            $this->table = $this->getTableName();
        }
    }

    public static function query(): QueryBuilder
    {
        $instance = new static();
        return new QueryBuilder($instance->database, $instance->table);
    }

    public function all(): array
    {
        $data = self::query()->get();
        return array_map(fn($row) => $this->hydrate($row), $data);
    }

    public function find(int|string $id): self|null
    {
        $data = self::query()->where($this->primaryKey, $id)->first();
        return $data ? $this->hydrate($data) : null;
    }

    protected function getTableName(): string
    {
        $className = (new \ReflectionClass($this))->getShortName();
        $purifiedClassName = preg_replace('/(?<!^)[A-Z]/', '_$0', $className);
        if (! is_string($purifiedClassName)) {
            throw new \Exception(
                "The class name {$className} is invalid."
            );
        }

        $snakeCase = strtolower($purifiedClassName);
        return $this->pluralize($snakeCase);
    }

    protected function pluralize(string $word): string
    {
        if (str_ends_with($word, 'y')) {
            return substr($word, 0, -1) . 'ies';
        }

        if (
            str_ends_with($word, 's') ||
            str_ends_with($word, 'x') ||
            str_ends_with($word, 'ch') ||
            str_ends_with($word, 'sh')
        ) {
            return $word . 'es';
        }

        return $word . 's';
    }

    public function update(): void
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

    public function save(): bool
    {
        $columns = [];
        $placeholders = [];
        $bindings = [];

        foreach ($this->attributes as $key => $value) {
            if ($key === $this->primaryKey && $value === null) {
                continue;
            }

            $columns[] = $key;
            $placeholders[] = ":{$key}";
            $bindings[$key] = $value;
        }

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->database->prepare($sql);
        $result = $stmt->execute($bindings);

        if ($result) {
            $this->attributes[$this->primaryKey] = $this->database->lastInsertId();
        }

        return $result;
    }

    public function delete(): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->database->prepare($sql);
        return $stmt->execute(['id' => $this->attributes[$this->primaryKey]]);
    }

    protected function hydrate(array $data): self
    {
        $instance = new static();
        $instance->attributes = $data;
        return $instance;
    }

    public function __isset(string $name): bool
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

    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        if (isset($this->belongsTo[$name])) {
            $foreignKey = $this->belongsTo[$name]['foreign_key'];
            $modelClass = $this->belongsTo[$name]['model'];

            /**
             * @var Model $relatedModel
             */
            $relatedModel = new $modelClass();
            return $relatedModel->find($this->{$foreignKey});
        }

        if (isset($this->hasMany[$name])) {
            $foreignKey = $this->hasMany[$name]['foreign_key'];
            $modelClass = $this->hasMany[$name]['model'];

            /**
             * @var Model $relatedModel
             */
            $relatedModel = new $modelClass();
            return $relatedModel::query()->where($foreignKey, $this->attributes['id'])->get();
        }

        return null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function offsetExists($offset): bool
    {
        return $this->__isset($offset);
    }

    public function offsetGet($offset): mixed
    {
        return $this->__get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            return;
        }
        $this->__set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->attributes[$offset]);
    }
}
