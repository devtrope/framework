<?php

namespace Ludens\Sphp;

use Ludens\Exceptions\ConfigurationFormatException;

final class ColonToken extends LexerToken
{
    public function validateNextType(array $tokens, int $position): mixed
    {
        /**
         * @var LexerToken
         */
        $nextToken = $tokens[$position + 1];
        if (
            LexerType::STRING !== $nextToken->getType() &&
            LexerType::NUMBER !== $nextToken->getType()
        ) {
            throw new ConfigurationFormatException(
                "Missing value after colon on line {$this->getLine()}"
            );
        }

        if (LexerType::NUMBER === $nextToken->getType()) {
            if (str_contains($nextToken->getValue(), '.')) {
                return (float)$nextToken->getValue();
            }
            return (int)$nextToken->getValue();
        }
        return $nextToken->getValue();
    }
}
