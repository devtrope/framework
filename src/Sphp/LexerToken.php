<?php

namespace Ludens\Sphp;

class LexerToken
{
    public function __construct(
        private LexerType $type,
        private string $value,
        private int $line
    ) {}

    public function getType(): LexerType
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
