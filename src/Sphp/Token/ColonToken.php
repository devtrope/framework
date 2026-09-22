<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\Grammar;
use Ludens\Sphp\Support\LexerType;

final class ColonToken extends LexerToken
{
    /**
     * @param int $line
     */
    public function __construct(int $line)
    {
        parent::__construct(LexerType::COLON, Grammar::COLON, $line);
    }
}
