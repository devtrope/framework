<?php

namespace Ludens\Http;

use Ludens\Core\Application;
use Ludens\Files\ImageUploader;
use Ludens\Http\Support\RequestData;
use Ludens\Http\Support\RequestHeaders;

/**
 * Represents an HTTP request and provides methods to access request data,
 * headers, and perform validation.
 *
 * @package Ludens\Http
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Request
{
    private string $uri;
    private string $method;
    private ?string $referer = null;
    private RequestHeaders $headers;
    private RequestData $data;
    private ImageUploader $imageUploader;

    /**
     * @param string $uri
     * @param string $method
     * @param RequestHeaders $headers
     * @param RequestData $data
     * @param string|null $referer
     */
    public function __construct(
        string $uri,
        string $method,
        RequestHeaders $headers,
        RequestData $data,
        ImageUploader $imageUploader,
        ?string $referer = null
    ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
        $this->data = $data;
        $this->imageUploader = $imageUploader;
        $this->referer = $referer;
    }

    /**
     * Create a Request from the current HTTP request.
     *
     * @return self
     */
    public static function capture(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $method = strtoupper($_SERVER['REQUEST_METHOD']) ?? 'GET';
        $referer = $_SERVER['HTTP_REFERER'] ?? null;

        $headers = RequestHeaders::capture();
        $data = RequestData::capture($headers);
        $imageUploader = new ImageUploader();

        return new self($uri, $method, $headers, $data, $imageUploader, $referer);
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Get request headers handler.
     *
     * @return RequestHeaders
     */
    public function headers(): RequestHeaders
    {
        return $this->headers;
    }

    /**
     * Get a specific header.
     *
     * @param string $name
     * @param mixed $default
     * @return string|null
     */
    public function header(string $name, ?string $default = null): string|null
    {
        return $this->headers->get($name, $default);
    }

    /**
     * Get a parameter value.
     *
     * @param string $key
     * @param mixed $default
     * @return string|array|null
     */
    public function get(string $key, ?string $default = null): string|array|null
    {
        return $this->data->get($key, $default);
    }

    /**
     * Get all parameters.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->data->all();
    }

    /**
     * Check if a parameter exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->data->has($key);
    }

    /**
     * Get JSON decoded data from the request body.
     *
     * @param string|null $key
     * @return mixed
     */
    public function json(?string $key = null): mixed
    {
        return $this->data->json($key);
    }

    /**
     * Get the raw request body.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->data->body();
    }

    /**
     * Check if the request content type is JSON.
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->headers()->isJson();
    }

    /**
     * Check if the request content type is form-urlencoded.
     *
     * @return bool
     */
    public function isFormUrlEncoded(): bool
    {
        return $this->headers()->isFormUrlEncoded();
    }

    /**
     * Check if the request content type is multipart/form-data
     *
     * @return bool
     */
    public function isFormData(): bool
    {
        return $this->headers()->isFormData();
    }

    /**
     * Check if the client expects a JSON response.
     *
     * @return bool
     */
    public function wantsJson(): bool
    {
        return $this->headers()->wantsJson();
    }

    /**
     * Get the referer URL.
     *
     * @return string|null
     */
    public function referer(): ?string
    {
        return $this->referer;
    }

    public function image(array $file): string
    {
        return $this->imageUploader->upload($file);
    }
}
