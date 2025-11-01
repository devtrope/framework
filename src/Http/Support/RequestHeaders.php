<?php

namespace Ludens\Http\Support;

/**
 * Handles HTTP request headers.
 *
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class RequestHeaders
{
    private array $headers;

    /**
     * @param array $headers
     */
    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    /**
     * Capture headers from the current request.
     *
     * @return self
     */
    public static function capture(): self
    {
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        return new self($headers ?: []);
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
     * Check if the request content type is JSON.
     *
     * @return bool
     */
    public function isJson(): bool
    {
        $contentType = $this->get('Content-Type', '');
        return str_contains($contentType, 'application/json');
    }

    /**
     * Check if the request content type is form URL encoded.
     *
     * @return bool
     */
    public function isFormUrlEncoded(): bool
    {
        $contentType = $this->get('Content-Type', '');
        return str_contains($contentType, 'application/x-www-form-urlencoded');
    }

    /**
     * Check if the request content type is a form data.
     *
     * @return bool
     */
    public function isFormData(): bool
    {
        $contentType = $this->get('Content-Type', '');
        return str_contains($contentType, 'multipart/form-data');
    }

    /**
     * Check if the client expects a JSON response.
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        $accept = $this->get('Accept', '');
        return str_contains($accept, 'application/json');
    }
}
