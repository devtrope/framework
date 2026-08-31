<?php

namespace Ludens\Sphp\Token;

use Override;

final class NumberToken extends LexerToken
{
    #[Override]
    public function getValue(): mixed
    {
        if (stripos($this->value, '.')) {
            return (float)$this->value;
        }
        return (int)$this->value;
    }
}
