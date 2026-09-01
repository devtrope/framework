<?php

namespace Ludens\Sphp;

use Ludens\Sphp\Support\LexerType;
use Ludens\Sphp\Token\BooleanToken;
use Ludens\Sphp\Token\ColonToken;
use Ludens\Sphp\Token\EndOfFileToken;
use Ludens\Sphp\Token\IdentifierToken;
use Ludens\Sphp\Token\IndentationToken;
use Ludens\Sphp\Token\LexerToken;
use Ludens\Sphp\Token\NumberToken;
use Ludens\Sphp\Token\StringToken;

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

            if (ctype_space($character)) {
                $spaces = 0;
                while (ctype_space($this->input[$this->position])) {
                    $spaces++;
                    $this->position++;
                }

                if ($spaces > 1) {
                    $tokens[] = new IndentationToken(LexerType::INDENTATION, $spaces, $this->line);
                }
                
                continue;
            }

            if (ctype_alpha($character) || '\\' === $character) {
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

        $tokens[] = new EndOfFileToken(LexerType::EOF, null, $this->line);
        return $tokens;
    }

    private function readIdentifier(): LexerToken
    {
        $value = null;
        while (
            $this->position < \strlen($this->input) &&
            (
                ctype_alpha($this->input[$this->position]) ||
                '\\' === $this->input[$this->position]
            )
        ) {
            $value .= $this->input[$this->position];
            $this->position++;
        }

        /**
         * There's no way to make a difference at this point between an identifier and a boolean value,
         * so, even if it's crappy, I will check it there based on the value of the identifier. It may cause a bug
         * if someone decides to call an identifier 'true' but who does this ???
         */
        if ('true' === $value || 'false' === $value) {
            return new BooleanToken(LexerType::BOOLEAN, $value, $this->line);
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
