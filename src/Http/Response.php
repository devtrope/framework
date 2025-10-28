<?php

namespace Ludens\Http;

use Ludens\Http\Responses\ViewResponse;
use Ludens\Http\Responses\JsonResponse;
use Ludens\Http\Responses\RedirectResponse;
use Ludens\Http\Support\ResponseHeaders;
use Ludens\Http\Support\SessionFlash;

class Response
{
    private string $body = '';
    private ResponseHeaders $headers;
    private SessionFlash $sessionFlash;
    private bool $sent = false; 
    private int $code = 200;

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

    public function withFlash(string $type, string $message): self
    {
        $this->sessionFlash->setFlash($type, $message);
        return $this;
    }

    public function success(string $message): self
    {
        $this->sessionFlash->setFlash('success', $message);
        return $this;
    }

    public function error(string $message): self
    {
        $this->sessionFlash->setFlash('error', $message);
        return $this;
    }

    public function info(string $message): self
    {
        $this->sessionFlash->setFlash('info', $message);
        return $this;
    }

    public function withErrors(array $errors): self
    {
        $this->sessionFlash->setErrors($errors);
        return $this;
    }

    public function withOldData(array $oldData): self
    {
        $this->sessionFlash->setOldData($oldData);
        return $this;
    }

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

    public function setBody(string $content): self
    {
        $this->body = $content;

        return $this;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers->set($name, $value);
        return $this;
    }

    public function header(string $key, ?string $default = null): string|null
    {
        return $this->headers->get($key, $default);
    }

    public function headers(): ResponseHeaders
    {
        return $this->headers;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function code(): int
    {
        return $this->code;
    }
}
