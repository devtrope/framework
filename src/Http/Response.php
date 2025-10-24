<?php

namespace Ludens\Http;

class Response
{
    private string $body = '';
    private array $headers = [];
    private bool $sent = false;
    private int $code = 200;

    public static function render(string $viewName, array $data = []): self
    {
        $filePath = rtrim(TEMPLATES_PATH, '/') . '/' . $viewName . '.php';

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

    public static function redirect(?string $url): self
    {
        if ($url === null) {
            throw new \Exception('URL for redirection cannot be null');
        }

        $response = new self();

        $response
            ->setHeader('Location', $url)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setBody('')
            ->setCode(302);

        return $response;
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

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

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
        $this->headers[$name] = $value;

        return $this;
    }

    public function header(?string $key): string|array|null
    {
        if ($key === null) {
            return $this->headers;
        }

        return $this->headers[$key] ?? null;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;
        http_response_code($code);

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
