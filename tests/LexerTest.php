<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Sphp\Lexer;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    public function testReturnsTheRightArray(): void
    {
        $lexer = new Lexer("title: 'Ludens'");
        $tokenized = $lexer->tokenize();
        $this->assertSame([
            ['type' => 'IDENTIFIER', 'value' => 'title', 'line' => 1],
            ['type' => 'COLON', 'value' => ':', 'line' => 1],
            ['type' => 'STRING', 'value' => 'Ludens', 'line' => 1]
        ], $tokenized);
    }
}
