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

        $this->clear();
    }

    public static function getInstance(): SessionFlash
    {
        if (self::$instance === null) {
            self::$instance = new SessionFlash();
        }

        return self::$instance;
    }

    /**
     * Set validation errors.
     *
     * @param array $errors
     * @return void
     */
    public function setErrors(array $errors): void
    {
        $_SESSION['errors'] = $errors;
        if (! in_array('errors', $_SESSION['_flash.new'])) {
            $_SESSION['_flash.new'][] = 'errors';
        }
    }

    /**
     * Get a validation error by key.
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
     * Clear old flash data from the session.
     *
     * @return void
     */
    public function clear(): void
    {
        $old = $_SESSION['_flash.old'] ?? [];

        foreach ($old as $key) {
            unset($_SESSION[$key]);
        }

        $_SESSION['_flash.old'] = $_SESSION['_flash.new'] ?? [];
        $_SESSION['_flash.new'] = [];
    }
}
