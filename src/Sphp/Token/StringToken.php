<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class StringToken extends LexerToken
{
    /**
     * @param string|null $value
     * @param int $line
     */
    public function __construct(string|null $value, int $line)
    {
        parent::__construct(LexerType::STRING, $value, $line);
    }
}
