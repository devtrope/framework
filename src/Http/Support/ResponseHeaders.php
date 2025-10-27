<?php

namespace Ludens\Http\Support;

/**
 * Handles HTTP response headers.
 * 
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ResponseHeaders
{
    private array $headers = [];

    /**
     * Set a response header.
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function set(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    /**
     * Get a specific header.
     *
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function get(string $name, ?string $default = null): string|null
    {
        return $this->headers[$name] ?? $default;
    }

    /**
     * Check if a header exists.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * Get all headers.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->headers;
    }

    /**
     * Remove a specific header.
     *
     * @param string $name
     * @return void
     */
    public function remove(string $name): void
    {
        unset($this->headers[$name]);
    }

    /**
     * Send all headers to the client.
     * 
     * Should only be called once in Response::send().
     *
     * @return void
     */
    public function send(): void
    {
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }
}
