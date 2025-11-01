<?php

namespace Ludens\Database;

use PDO;
use Exception;

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
        return new PDO(
            "mysql:host=localhost;
            dbname=blog_test;
            charset=utf8mb4;
            port=3306",
            "root",
            "root",
            [
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            ]
        );
    }
}
