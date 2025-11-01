<?php

namespace Ludens\Http\Support;

/**
 * Manage session flash data, errors, and old input data.
 *
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class SessionFlash
{
    private static ?SessionFlash $instance = null;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->age();
    }

    /**
     * Get the singleton instance of SessionFlash.
     *
     * @return SessionFlash
     */
    public static function getInstance(): SessionFlash
    {
        if (self::$instance === null) {
            self::$instance = new SessionFlash();
        }

        return self::$instance;
    }

    /**
     * Set a flash message that will be available on the next request only.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setFlash(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
        if (! in_array($key, $_SESSION['_flash.new'])) {
            $_SESSION['_flash.new'][] = $key;
        }
    }

    /**
     * Get a flash message.
     *
     * @param string $key
     * @return string|null
     */
    public function flash(string $key, ?string $default = null): string|null
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a flash message exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasFlash(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Set validation errors.
     *
     * @param array $errors
     * @return void
     */
    public function setErrors(array $errors): void
    {
        $this->setFlash('errors', $errors);
    }

    /**
     * Get a specific validation error.
     *
     * @param string $key
     * @return string|null
     */
    public function error(string $key): string|null
    {
        return $_SESSION['errors'][$key] ?? null;
    }


    /**
     * Check if a validation error exists for a given key.
     *
     * @param string $key
     * @return bool
     */
    public function hasError(string $key): bool
    {
        return isset($_SESSION['errors'][$key]);
    }

    /**
     * Set old input data (for form repopulation after validation errors).
     *
     * @param array $old
     * @return void
     */
    public function setOldData(array $old): void
    {
        $this->setFlash('old', $old);
    }

    /**
     * Get a specific old input value.
     *
     * @param string $key
     * @return string|null
     */
    public function oldData(string $key): string|null
    {
        return $_SESSION['old'][$key] ?? null;
    }

    /**
     * Age flash data - move current flash to old, prepare for new flash.
     *
     * @return void
     */
    public function age(): void
    {
        // Delete old flash data
        $old = $_SESSION['_flash.old'] ?? [];

        foreach ($old as $key) {
            unset($_SESSION[$key]);
        }

        // Move new flash data to old
        $_SESSION['_flash.old'] = $_SESSION['_flash.new'] ?? [];
        $_SESSION['_flash.new'] = [];
    }
}
