<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;

class LexerToken
{
    /**
     * Summary of __construct
     * @param LexerType $type
     * @param int|float|string|null|bool|array<mixed> $value
     * @param int $line
     */
    public function __construct(protected LexerType $type, protected int|float|string|null|bool|array $value, protected int $line)
    {}

    /**
     * @return LexerType
     */
    public function getType(): LexerType
    {
        return $this->type;
    }

    /**
     * @return int|float|string|null|bool|array<mixed>
     */
    public function getValue(): int|float|string|null|bool|array
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }
}
