<?php

namespace Ludens\Http;

final class Response
{
    private string $body;

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setCode(int $code = 200): self
    {
        http_response_code($code);
        return $this;
    }

    public function send(): self
    {
        echo $this->body;
        return $this;
    }
}
