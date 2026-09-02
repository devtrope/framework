<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class EndOfFileToken extends LexerToken
{
    public function __construct(int $line)
    {
        return parent::__construct(LexerType::EOF, null, $line);
    }
}
