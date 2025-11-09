<?php

namespace Ludens\Exceptions;

use PDOException;

/**
 * Exception thrown for database-related errors.
 */
class DatabaseException extends \RuntimeException
{
    public static function connectionFailed(PDOException $previous): self
    {
        return new self(
            "Database connection failed: {$previous->getMessage()}",
            (int) $previous->getCode(),
            $previous
        );
    }
}
