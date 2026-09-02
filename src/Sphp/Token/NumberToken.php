<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;
use Override;

final class NumberToken extends LexerToken
{
    public function __construct(mixed $value, int $line)
    {
        return parent::__construct(LexerType::NUMBER, $value, $line);
    }

    #[Override]
    public function getValue(): mixed
    {
        if (stripos($this->value, '.')) {
            return (float)$this->value;
        }
        return (int)$this->value;
    }
}
