<?php

namespace Ludens\Http;

use Ludens\Http\Support\HttpResponseCode;

final class Response
{
    /**
     * @var string
     */
    private string $body;

    /**
     * @param string $body
     * @return Response
     */
    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param HttpResponseCode $code
     * @return Response
     */
    public function setCode(HttpResponseCode $code = HttpResponseCode::OK): self
    {
        http_response_code($code->value);
        return $this;
    }

    /**
     * @return Response
     */
    public function send(): self
    {
        echo $this->body;
        return $this;
    }
}
