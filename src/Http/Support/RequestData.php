<?php

namespace Ludens\Http\Support;

/**
 * Handles request data from various sources (query, body, JSON).Handles request data from various sources (query, body, JSON).
 * 
 * @package Ludens\Http\Support
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class RequestData
{
    private array $data = [];
    private ?array $jsonData = null;
    private string $rawBody;

    /**
     * @param array $data
     * @param string $rawBody
     */
    public function __construct(array $data = [], string $rawBody = '')
    {
        $this->data = $data;
        $this->rawBody = $rawBody;
    }

    /**
     * Capture request data from the current HTTP request.
     *
     * @param RequestHeaders $headers
     * @return self
     */
    public static function capture(RequestHeaders $headers): self
    {
        $rawBody = file_get_contents('php://input') ?: '';
        $data = self::extractData($headers, $rawBody);

        return new self($data, $rawBody);
    }

    /**
     * Get a specific key from the request data.
     *
     * @param string $key
     * @param string|null $default
     * @return string|array|null
     */
    public function get(string $key, ?string $default = null): string|array|null
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get all request data.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Check if a key exists in the request data.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get JSON decoded data from the request body.
     *
     * @param string|null $key
     * @return mixed
     */
    public function json(?string $key = null): mixed
    {
        if ($this->jsonData === null) {
            $this->jsonData = json_decode($this->rawBody, true) ?? [];
        }

        if ($key === null) {
            return $this->jsonData;
        }

        return $this->jsonData[$key] ?? null;
    }

    /**
     * Get the raw body of the request.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->rawBody;
    }

    /**
     * Check if request has JSON data.
     *
     * @return bool
     */
    public function hasJson(): bool
    {
        return ! empty($this->rawBody) && json_decode($this->rawBody) !== null;
    }

    /**
     * Extract data from various sources based on content type.
     *
     * @param RequestHeaders $headers
     * @param string $rawBody
     * @return array
     */
    private static function extractData(RequestHeaders $headers, string $rawBody): array
    {
        $data = $_GET;

        if ($headers->isJson() && ! empty($rawBody)) {
            $jsonData = json_decode($rawBody, true);

            if (is_array($jsonData)) {
                return array_merge($data, $jsonData);
            }
        }

        if ($headers->isFormUrlEncoded()) {
            parse_str($rawBody, $parsedData);

            if (is_array($parsedData)) {
                return array_merge($data, $parsedData);
            }
        }

        if ($headers->isFormData()) {
            return array_merge($data, $_POST, $_FILES);
        }

        return array_merge($data, $_POST);
    }
}