<?php

namespace Ludens\Sphp;

use Exception;

final class Sphp
{
    private array $result = [];

    public function parse(string $filepath): array
    {
        $lexer = new Lexer(file_get_contents($filepath));
        $tokens = $lexer->tokenize();
        for ($i = 0; $i < \count($tokens); $i++) {
            if ($tokens[$i]['type'] === LexerType::IDENTIFIER->name) {
                $colonIndex = $i + 1;
                if (
                    false === isset($tokens[$colonIndex]) ||
                    LexerType::COLON->name !== $tokens[$colonIndex]['type']
                ) {
                    throw new Exception();
                }

                $valueIndex = $i + 2;
                if (
                    false === isset($tokens[$valueIndex]) ||
                    (
                        LexerType::STRING->name !== $tokens[$valueIndex]['type'] &&
                        LexerType::NUMBER->name !== $tokens[$valueIndex]['type']
                    )
                ) {
                    throw new Exception();
                }
                $value = $tokens[$valueIndex]['value'];
                if (LexerType::NUMBER->name === $tokens[$valueIndex]['type']) {
                    if (str_contains($value, '.')) {
                        $value = (float)$value;
                    } else {
                        $value = (int)$value;
                    }
                }
                $this->result[$tokens[$i]['value']] = $value;
            }
        }
        return $this->result;
    }
}
