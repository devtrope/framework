<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

final class IdentifierToken extends LexerToken
{
    public function __construct(mixed $value, int $line)
    {
        return parent::__construct(LexerType::IDENTIFIER, $value, $line);
    }
}
