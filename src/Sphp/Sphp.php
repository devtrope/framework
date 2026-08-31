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
        while ($this->position < \count($this->tokens)) {
            [$key, $value] = $this->parseEntry();
            $result[$key] = $value;
        }
        return $result;
    }

    private function parseEntry(): array
    {
        /**
         * @var LexerToken
         */
        $token =  $this->tokens[$this->position];
        $this->expect(LexerType::IDENTIFIER);
        $identifier = $token->getValue();
        $this->consume();
        $this->expect(LexerType::COLON);
        $this->consume();
        
        return [$identifier, $this->parseValue()];
    }

    private function parseValue(): mixed
    {
        /**
         * @var LexerToken
         */
        $token =  $this->tokens[$this->position];
        if (LexerType::STRING === $token->getType()) {
            $this->expect(LexerType::STRING);
        } else {
            $this->expect(LexerType::NUMBER);
        }

        $value = $token->getValue();
        $this->consume();

        return $value;
    }

    private function expect(LexerType $expected): void
    {
        /**
         * @var LexerToken
         */
        $token =  $this->tokens[$this->position];
        if ($expected !== $token->getType()) {
            throw new ConfigurationFormatException(
                "Invalid token on line {$token->getLine()}"
            );
        }
    }

    private function consume(): void
    {
        $this->position++;
    }
}
