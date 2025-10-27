<?php

namespace Ludens\Http\Support;

/**
 * Class to manage session flash data, errors, and old input data.
 * 
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class SessionFlash
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /** 
     * Set a flash message.
     *
     * @param string $type
     * @param string $message
     * @return void
     */
    public function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /**
     * Get a flash message by key.
     *
     * @param string $key
     * @return string|null
     */
    public function flash(string $key): string|null
    {
        return $_SESSION['flash'][$key] ?? null;
    }

    /**
     * Check if a flash message exists.
     *
     * @return bool
     */
    public function hasFlash(): bool
    {
        return isset($_SESSION['flash']);
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
     * Set old input data.
     *
     * @param array $oldData
     * @return void
     */
    public function setOldData(array $oldData): void
    {
        $_SESSION['old'] = $oldData;
    }

    /**
     * Get old input data by key.
     *
     * @param string $key
     * @return string|null
     */
    public function oldData(string $key): string|null
    {
        return $_SESSION['old'][$key] ?? null;
    }

    /**
     * Clear flash data.
     *
     * @return void
     */
    public function clearFlash(): void
    {
        unset($_SESSION['flash']);
    }

    /**
     * Clear validation errors.
     *
     * @return void
     */
    public function clearErrors(): void
    {
        unset($_SESSION['errors']);
    }

    /**
     * Clear old input data.
     *
     * @return void
     */
    public function clearOldData(): void
    {
        unset($_SESSION['old']);
    }

    /**
     * Clear all session flash data, errors, and old input data.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->clearFlash();
        $this->clearErrors();
        $this->clearOldData();
    }
}
