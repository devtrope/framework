<?php

namespace Ludens\Sphp;

use Ludens\Exceptions\ConfigurationFormatException;
use Ludens\Sphp\Support\LexerType;
use Ludens\Sphp\Token\LexerToken;

final class Sphp
{
    private array $tokens = [];
    private int $position = 0;

    public function parse(string $filepath): array
    {
        $result = [];
        $lexer = new Lexer(file_get_contents($filepath));
        $this->tokens = $lexer->tokenize();

        while (
            $this->position < \count($this->tokens) &&
            LexerType::EOF !== $this->peek()->getType()
        ) {
            [$identifier, $value] = $this->parseEntry();
            $result[$identifier] = $value;
        }
        return $result;
    }

    private function parseEntry(): array
    {
        $this->expect(LexerType::IDENTIFIER);
        $identifier = $this->consume()->getValue();
        $this->expect(LexerType::COLON);
        $this->consume();
        
        return [$identifier, $this->parseValue()];
    }

    private function parseValue(): mixed
    {
        if (LexerType::INDENTATION === $this->peek()->getType()) {
            return $this->parseArray();
        }

        if (LexerType::STRING === $this->peek()->getType()) {
            $this->expect(LexerType::STRING);
            return $this->consume()->getValue();
        }

        if (LexerType::BOOLEAN === $this->peek()->getType()) {
            $this->expect(LexerType::BOOLEAN);
            return (bool)$this->consume()->getValue();
        }

        $this->expect(LexerType::NUMBER);
        return $this->consume()->getValue();
    }

    private function parseArray(): array
    {
        $this->expect(LexerType::INDENTATION);
        $indentation = $this->consume()->getValue();
        $result = [];

        [$identifier, $value] = $this->parseEntry();
        $result[$identifier] = $value;

        /**
         * Only continue this array while the following entries are at
         * the same depth. Anything deeper or shallower is not ours.
         */
        while (
            LexerType::INDENTATION === $this->peek()->getType() &&
            $indentation === $this->peek()->getValue()
        ) {
            $this->consume();
            [$identifier, $value] = $this->parseEntry();
            $result[$identifier] = $value;
        }

        return $result;
    }

    private function expect(LexerType $expected): void
    {
        if ($expected !== $this->peek()->getType()) {
            throw new ConfigurationFormatException(
                "Invalid token on line {$this->peek()->getLine()}"
            );
        }
    }

    private function consume(): LexerToken
    {
        $token = $this->tokens[$this->position];
        $this->position++;
        return $token;
    }

    private function peek(): LexerToken
    {
        return $this->tokens[$this->position];
    }
}
