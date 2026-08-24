<?php

namespace Ludens\Sphp;

use Ludens\Exceptions\ConfigurationFormatException;

final class IdentifierToken extends LexerToken
{
    public function validateNextType(array $tokens, int $position): mixed
    {
        /**
         * @var LexerToken
         */
        $nextToken = $tokens[$position + 1];
        if (LexerType::COLON !== $nextToken->getType()) {
            throw new ConfigurationFormatException(
                "Missing colon after identifier on line {$this->getLine()}"
            );
        }

        /**
         * @var ColonToken $nextToken
         */
        return $nextToken->validateNextType($tokens, $position + 1);
    }
}
