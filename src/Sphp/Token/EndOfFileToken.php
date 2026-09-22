<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class EndOfFileToken extends LexerToken
{
    /**
     * @param int $line
     */
    public function __construct(int $line)
    {
        parent::__construct(LexerType::EOF, null, $line);
    }
}
