<?php

namespace Ludens\Sphp;

use Ludens\Sphp\LexerType;

final class Lexer
{
    private const COLON = ":";
    private const QUOTE = "'";
    private const NEW_LINE = "\n";
    private int $position = 0;
    private int $line = 1;

    public function __construct(
        private string $input
    ) {}

    public function tokenize(): array
    {
        $tokens = [];

        while ($this->position < \strlen($this->input)) {
            $character = $this->input[$this->position];

            if (self::NEW_LINE === $character) {
                $this->line++;
                $this->position++;
                continue;
            }

            if (ctype_alpha($character)) {
                $tokens[] = $this->readIdentifier();
                continue;
            }
            
            if (ctype_digit($character)) {
                $tokens[] = $this->readNumber();
                continue;
            }

            if (self::QUOTE === $character) {
                $tokens[] = $this->readString();
                //Skip the last quote closing the string value
                $this->position++;
                continue;
            }

            if (self::COLON === $character) {
                $tokens[] = new ColonToken(LexerType::COLON, self::COLON, $this->line);
                $this->position++;
                continue;
            }

            $this->position++;
        }

        return $tokens;
    }

    private function readIdentifier(): LexerToken
    {
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            ctype_alpha($this->input[$this->position])
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return new IdentifierToken(LexerType::IDENTIFIER, $value, $this->line);
    }

    private function readNumber(): LexerToken
    {
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            null !== $this->input[$this->position] && 
            (
                ctype_digit($this->input[$this->position]) ||
                '.' === $this->input[$this->position]
            )
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return new NumberToken(LexerType::NUMBER, $value, $this->line);
    }

    private function readString(): LexerToken
    {
        // A string type starts with a quote, so we want to move forward
        // to store the real string value
        $this->position++;
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            self::QUOTE !== $this->input[$this->position]
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }
        return new StringToken(LexerType::STRING, $value, $this->line);
    }
}
