<?php

namespace Ludens\Contracts;

use Ludens\Http\Support\HttpMethod;

interface HttpMethodAttributeInterface
{
    public function getPath(): string;
    public function getHttpMethod(): HttpMethod;
}
