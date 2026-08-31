<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

class LexerToken
{
    public function __construct(
        protected LexerType $type,
        protected mixed $value,
        protected int $line
    ) {}

    public function getType(): LexerType
    {
        return $this->type;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
