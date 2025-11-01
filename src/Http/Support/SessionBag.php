<?php

namespace Ludens\Http\Support;

/**
 * Type-safe wrapper for $_SESSION superglobal.
 *
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class SessionBag
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Get a session value as string.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getString(string $key, ?string $default = null): string|null
    {
        $value = $_SESSION[$key] ?? $default;
        return is_string($value) ? $value : $default;
    }

    /**
     * Get a session value as array.
     *
     * @param string $key
     * @param array|null $default
     * @return array|null
     */
    public function getArray(string $key, ?array $default = null): array|null
    {
        $value = $_SESSION[$key] ?? $default;
        return is_array($value) ? $value : $default;
    }

    /**
     * Get a session value (mixed type).
     *
     * @param string $key
     * @param string|null $default
     * @return mixed
     */
    public function get(string $key, ?string $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if a session key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Get all session data.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * Remove a session key.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }


    /**
     * Clear all session data.
     *
     * @return void
     */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * Destroy the session.
     *
     * @return void
     */
    public function destroy(): void
    {
        session_destroy();
    }
}
