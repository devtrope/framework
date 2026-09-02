<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class StringToken extends LexerToken
{
    public function __construct(mixed $value, int $line)
    {
        return parent::__construct(LexerType::STRING, $value, $line);
    }
}
