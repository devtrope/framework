<?php

namespace Ludens\Sphp;

use Ludens\Exceptions\ConfigurationFormatException;

final class Sphp
{
    private array $result = [];

    public function parse(string $filepath): array
    {
        $lexer = new Lexer(file_get_contents($filepath));
        $tokens = $lexer->tokenize();
        $position = 0;
        foreach ($tokens as $token) {
            if (LexerType::IDENTIFIER === $token->getType()) {
                $value = $token->validateNextType($tokens, $position);
                $this->result[$token->getValue()] = $value;
            }
            $position++;
        }
        return $this->result;
    }
}
