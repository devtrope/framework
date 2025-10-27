<?php

namespace Ludens\Http;

use Ludens\Core\Application;
use Ludens\Http\Responses\RedirectResponse;
use Ludens\Http\Support\ResponseHeaders;

class Response
{
    private string $body = '';
    private ResponseHeaders $headers;
    private bool $sent = false;
    private int $code = 200;

    /**
     * Constructor to initialize headers.
     */
    public function __construct()
    {
        $this->headers = new ResponseHeaders();
    }

    public static function render(string $viewName, array $data = []): self
    {
        $templatesPath = rtrim(Application::templates());

        if (! is_dir($templatesPath)) {
            throw new \Exception("$templatesPath does not exist");
        }

        $filePath = rtrim(Application::templates(), '/') . '/' . $viewName . '.php';

        if (! file_exists($filePath)) {
            throw new \Exception("View file $viewName does not exist");
        }

        ob_start();
        extract($data, EXTR_SKIP);
        include $filePath;
        $content = ob_get_clean();

        if (! $content) {
            $content = '';
        }

        $response = new self();
        
        $response
            ->setBody($content)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8');
        
        return $response;
    }

    public static function redirect(?string $url, int $statusCode = 302): self
    {
        return new RedirectResponse($url, $statusCode);
    }

    public function withFlash(string $type, string $message): self
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];

        return $this;
    }

    public function withErrors(array $errors): self
    {
        $_SESSION['errors'] = $errors;

        return $this;
    }

    public function withOldData(array $oldData): self
    {
        $_SESSION['old'] = $oldData;

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

    public static function json(array $data): self
    {
        $response = new self();

        $jsonData = json_encode($data);

        if (! $jsonData) {
            throw new \Exception('Failed to encode data to JSON');
        }

        $response
            ->setBody($jsonData)
            ->setHeader('Content-Type', 'application/json; charset=UTF-8');

        return $response;
    }
}
