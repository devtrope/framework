<?php

namespace Ludens\Database;

use PDO;
use Exception;
use PDOException;
use Ludens\Core\Application;
use Ludens\Exceptions\DatabaseException;

/**
 * Database connection manager (Singleton).
 *
 * Manages a single PDO connection instance for the application.
 *
 * @package Ludens\Database
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ConnectionManager
{
    private static ?self $instance = null;
    private ?PDO $connection = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Prevent cloning of the instance.
     *
     * @return void
     */
    private function __clone(): void
    {
    }

    /**
     * Prevent unserializing of the instance.
     *
     * @return never
     *
     * @throws \Exception
     */
    public function __wakeup(): void
    {
        throw new Exception("Cannot unserialize singleton.");
    }

    /**
     * Get the singleton instance.
     *
     * @return ConnectionManager
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get the PDO connection (creates if not exists)
     *
     * @return PDO
     *
     * @throws DatabaseException
     */
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connection = $this->createConnection();
        }

        return $this->connection;
    }

    /**
     * Create a new PDO connection.
     *
     * @return PDO
     *
     * @throws DatabaseException
     */
    private function createConnection(): PDO
    {
        $app = Application::getInstance();

        try {
            /**
             * TODO: Different drivers
             */

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $app->config('database.host', 'localhost'),
                $app->config('database.port', 3306),
                $app->config('database.name'),
                $app->config('database.charset', 'utf8mb4')
            );

            $connection = new PDO(
                $dsn,
                $app->config('database.username'),
                $app->config('database.password'),
                $this->getDefaultOptions()
            );

            return $connection;
        } catch (PDOException  $e) {
            throw DatabaseException::connectionFailed($e);
        }
    }

    /**
     * Get default PDO options.
     *
     * @return array<bool|int>
     */
    private function getDefaultOptions(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ];
    }
}
