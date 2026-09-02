<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class NullToken extends LexerToken
{
    public function __construct(int $line)
    {
        return parent::__construct(LexerType::NULL, null, $line);
    }
}
