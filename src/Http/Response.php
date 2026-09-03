<?php

namespace Ludens\Http;

use Ludens\Http\Support\HttpResponseCode;

final class Response
{
    private string $body;

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setCode(HttpResponseCode $code = HttpResponseCode::OK): self
    {
        http_response_code($code->value);
        return $this;
    }

    public function send(): self
    {
        echo $this->body;
        return $this;
    }
}
