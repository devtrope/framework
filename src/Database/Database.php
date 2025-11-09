<?php

namespace Ludens\Database;

use PDO;
use Exception;
use Ludens\Core\Application;

class Database
{
    private static ?Database $instance = null;

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
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function connect(): PDO
    {
        $app = Application::getInstance();

        return new PDO(
            "mysql:host={$app->config('database.host')};
            dbname={$app->config('database.name')};
            charset=utf8mb4;
            port={$app->config('database.port')}",
            "{$app->config('database.username')}",
            "{$app->config('database.password')}",
            [
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
