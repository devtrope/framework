<?php

namespace Ludens;

use Attribute;

#[Attribute]
final class Get
{
    private string $url;

    public function url(string $url)
    {
        $this->url = $url;
    }
}
