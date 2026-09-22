<?php

namespace Ludens\Http\Support;

use UnexpectedValueException;

final class Server
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(private array $parameters)
    {}

    /**
     * @return Server
     */
    public static function fromGlobals(): self
    {
        return new self($_SERVER);
    }

    /**
     * @param string $key
     * @throws UnexpectedValueException
     * @return string
     */
    public function get(string $key): string
    {
        $value = null;
        if (isset($this->parameters[$key])) {
            $value = $this->parameters[$key];
        }

        if (false === \is_string($value)) {
            throw new UnexpectedValueException(sprintf(
                'Expected $_SERVER[\'%s\'] to be a string, got %s.',
                $key,
                get_debug_type($value)
            ));
        }
        return $value;
    }
}
