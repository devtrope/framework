<?php

namespace Ludens\Http\Support;

/**
 * Type-safe wrapper for $_SERVER superglobal.
 *
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ServerBag
{
    public function __construct(
        private array $server = []
    ) {
    }

    /**
     * Create from $_SERVER superglobal.
     *
     * @return self
     */
    public static function fromGlobal(): self
    {
        return new self($_SERVER);
    }

    /**
     * Return a specific server key and ensure it's a string
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function get(string $key, ?string $default = null): string|null
    {
        $value = $this->server[$key] ?? $default;
        return is_string($value) ? $value : $default;
    }

    /**
     * Check if a server variable exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->server[$key]);
    }

    /**
     * Get request URI.
     *
     * @return string
     */
    public function getRequestUri(): string
    {
        return (string) $this->get('REQUEST_URI', '/');
    }

    /**
     * Get request method.
     *
     * @return string
     */
    public function getRequestMethod(): string
    {
        return strtoupper((string) $this->get('REQUEST_METHOD', 'GET'));
    }

    /**
     * Get HTTP referer
     *
     * @return string|null
     */
    public function getReferer(): string|null
    {
        return $this->get('HTTP_REFERER');
    }

    /**
     * Get all server variables.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->server;
    }
}
