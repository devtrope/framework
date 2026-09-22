<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;
use Override;

final class BooleanToken extends LexerToken
{
    /**
     * @param string $value
     * @param int $line
     */
    public function __construct(string $value, int $line)
    {
        parent::__construct(LexerType::BOOLEAN, $value, $line);
    }

    #[Override]
    public function getValue(): bool
    {
        return (bool)$this->value;
    }
}
