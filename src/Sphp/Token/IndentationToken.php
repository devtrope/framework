<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class IndentationToken extends LexerToken
{
    /**
     * @param int $value
     * @param int $line
     */
    public function __construct(int $value, int $line)
    {
        parent::__construct(LexerType::INDENTATION, $value, $line);
    }
}
