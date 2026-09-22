<?php

namespace Ludens\Http;

use Ludens\Http\Support\HttpMethod;
use Ludens\Http\Support\Server;

final class Request
{
    /**
     * @param HttpMethod $httpMethod
     * @param string $path
     */
    public function __construct(private HttpMethod $httpMethod, private string $path)
    {}

    /**
     * @return Request
     */
    public static function fromGlobals(): self
    {
        $server = Server::fromGlobals();
        
        return new self(
            HttpMethod::from($server->get('REQUEST_METHOD')),
            $server->get('REQUEST_URI')
        );
    }

    /**
     * @return HttpMethod
     */
    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
