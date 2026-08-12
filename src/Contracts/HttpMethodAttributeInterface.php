<?php

namespace Ludens\Contracts;

interface HttpMethodAttributeInterface
{
    public function getPath(): string;
    public function getHttpMethod(): string;
}
