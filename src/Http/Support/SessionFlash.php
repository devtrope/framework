<?php

namespace Ludens\Http\Support;

use Ludens\Http\Support\SessionBag;

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
    private SessionBag $session;

    public function __construct()
    {
        $this->session = new SessionBag();
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
        $this->session->set($key, $value);

        /**
         * @var array $flashNew
         */
        $flashNew = $this->session->getArray('_flash.new', []);
        if (! in_array($key, $flashNew)) {
            $flashNew[] = $key;
            $this->session->set('_flash.new', $flashNew);
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
        return $this->session->getString($key, $default);
    }

    /**
     * Check if a flash message exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasFlash(string $key): bool
    {
        return $this->session->has($key);
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
     * Get all validation errors.
     *
     * @return array<string, string>
     */
    public function errors(): array
    {
        /**
         * @var array $errors
         */
        $errors = $this->session->getArray('errors', []);
        return $errors;
    }

    /**
     * Get a specific validation error.
     *
     * @param string $key
     * @return string|null
     */
    public function error(string $key): string|null
    {
        $errors = $this->errors();
        return is_string($errors[$key] ?? null) ? $errors[$key] : null;
    }

    /**
     * Check if a validation error exists for a given key.
     *
     * @param string $key
     * @return bool
     */
    public function hasError(string $key): bool
    {
        $errors = $this->errors();
        return isset($errors[$key]);
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
        $old = $this->session->getArray('old', []);
        return $old[$key] ?? null;
    }

    /**
     * Age flash data - move current flash to old, prepare for new flash.
     *
     * @return void
     */
    public function age(): void
    {
        // Delete old flash data
        /**
         * @var array $old
         */
        $old = $this->session->getArray('_flash.old', []);

        foreach ($old as $key) {
            $this->session->remove($key);
        }

        $new = $this->session->getArray('_flash.new', []);

        // Move new flash data to old
        $this->session->set('_flash.old', $new);
        $this->session->set('_flash.new', []);
    }
}
