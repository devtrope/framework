<?php

namespace Ludens\Sphp\Token;

use Ludens\Sphp\Support\LexerType;
use Override;
use UnexpectedValueException;

final class NumberToken extends LexerToken
{
    /**
     * @param string|null $value
     * @param int $line
     */
    public function __construct(string|null $value, int $line)
    {
        parent::__construct(LexerType::NUMBER, $value, $line);
    }

    #[Override]
    public function getValue(): int|float
    {
        if (false === \is_string($this->value)) {
            throw new UnexpectedValueException(\sprintf(
                'Cannot convert value of type %s to int|float.',
                get_debug_type($this->value),
            ));
        }

        if (stripos($this->value, '.')) {
            return (float)$this->value;
        }
        return (int)$this->value;
    }
}
