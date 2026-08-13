<?php

namespace Ludens\Exceptions;

use Exception;
use Throwable;

final class InvalidControllerException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable|null $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }
}
