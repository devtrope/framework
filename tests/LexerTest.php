<?php

declare(strict_types=1);

namespace Tests;

use Ludens\Sphp\Lexer;
use PHPUnit\Framework\TestCase;

final class LexerTest extends TestCase
{
    public function testReturnsTheRightArray(): void
    {
        $lexer = new Lexer();
        $tokenized = $lexer->tokenize("title: 'Ludens'");
        $this->assertSame([
            ['IDENTIFIER' => 'title'],
            ['COLON' => ':'],
            ['STRING' => 'Ludens']
        ], $tokenized);
    }
}
