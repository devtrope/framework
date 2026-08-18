<?php

namespace Ludens\Sphp;

enum LexerType: string
{
    case IDENTIFIER = 'IDENTIFIER';
    case STRING = 'STRING';
    case NUMBER = 'NUMBER';
    case COLON = 'COLON';
}
