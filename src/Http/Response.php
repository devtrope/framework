<?php

namespace Ludens\Http;

use Ludens\Http\Responses\ViewResponse;
use Ludens\Http\Responses\JsonResponse;
use Ludens\Http\Responses\RedirectResponse;
use Ludens\Http\Support\ResponseHeaders;
use Ludens\Http\Support\SessionFlash;

/**
 * Represents an HTTP Response and provides methodes to manage and send responses.
 * 
 * @package Ludens\Http
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Response
{
    private string $body = '';
    private bool $sent = false; 
    private int $code = 200;
    private ResponseHeaders $headers;
    private SessionFlash $sessionFlash;

    /**
     * Constructor to initialize headers and session flash.
     */
    public function __construct()
    {
        $this->headers = new ResponseHeaders();
        $this->sessionFlash = $sessionFlash ?? SessionFlash::getInstance();
    }

    /**
     * Render a view template.
     *
     * @param string $viewName
     * @param array $data
     * @return ViewResponse
     */
    public static function view(string $viewName, array $data = []): self
    {
        return new ViewResponse($viewName, $data);
    }

    /**
     * Create a redirect response.
     *
     * @param string|null $url
     * @param int $statusCode
     * @return RedirectResponse
     */
    public static function redirect(?string $url, int $statusCode = 302): self
    {
        return new RedirectResponse($url, $statusCode);
    }

    /**
     * Create a JSON response.
     *
     * @param array $data
     * @return JsonResponse
     */
    public static function json(array $data): self
    {
        return new JsonResponse($data);
    }

    /**
     * Add a flash message to the response.
     *
     * @param string $type
     * @param string $message
     * @return self
     */
    public function withFlash(string $type, string $message): self
    {
        $this->sessionFlash->setFlash($type, $message);
        return $this;
    }

    /**
     * Add a success flash message.
     *
     * @param string $message
     * @return self
     */
    public function success(string $message): self
    {
        $this->sessionFlash->setFlash('success', $message);
        return $this;
    }

    /**
     * Add an error flash message.
     *
     * @param string $message
     * @return self
     */
    public function error(string $message): self
    {
        $this->sessionFlash->setFlash('error', $message);
        return $this;
    }

    /**
     * Add an info flash message.
     *
     * @param string $message
     * @return self
     */
    public function info(string $message): self
    {
        $this->sessionFlash->setFlash('info', $message);
        return $this;
    }

    /**
     * Attach validation errors to the response.
     *
     * @param array $errors
     * @return self
     */
    public function withErrors(array $errors): self
    {
        $this->sessionFlash->setErrors($errors);
        return $this;
    }

    /**
     * Attach old input data to the response.
     *
     * @param array $oldData
     * @return self
     */
    public function withOldData(array $oldData): self
    {
        $this->sessionFlash->setOldData($oldData);
        return $this;
    }

    /**
     * Send the response to the client.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function send(): void
    {
        if ($this->sent) {
            throw new \Exception('Response has already been sent');
        }

        http_response_code($this->code);
        $this->headers->send();
        echo $this->body;

        $this->sent = true;
    }

    /**
     * Set the response body content.
     *
     * @param string $content
     * @return self
     */
    public function setBody(string $content): self
    {
        $this->body = $content;
        return $this;
    }

    /**
     * Get the response body content.
     *
     * @return string
     */
    public function body(): string
    {
        return $this->body;
    }

    /**
     * Set a response header.
     *
     * @param string $name
     * @param string $value
     * @return self
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers->set($name, $value);
        return $this;
    }

    /**
     * Get a response header.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function header(string $key, ?string $default = null): string|null
    {
        return $this->headers->get($key, $default);
    }

    /**
     * Get all response headers.
     *
     * @return ResponseHeaders
     */
    public function headers(): ResponseHeaders
    {
        return $this->headers;
    }

    /**
     * Set the HTTP status code for the response.
     *
     * @param int $code
     * @return self
     */
    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get the HTTP status code of the response.
     *
     * @return int
     */
    public function code(): int
    {
        return $this->code;
    }
}
