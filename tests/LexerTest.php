<?php

use \Aegis\Lexer;
use \Aegis\Token;

class LexerTest extends PHPUnit_Framework_TestCase
{
    public function testText()
    {
        $this->tokenTypeTest('', []);

        $this->tokenTypeTest('s', [
            Token::T_TEXT,
        ]);

        $this->tokenTypeTest('1', [
            Token::T_TEXT,
        ]);

        $this->tokenTypeTest('512', [
            Token::T_TEXT,
        ]);

        $this->tokenTypeTest('test', [
            Token::T_TEXT,
        ]);

        $this->tokenTypeTest('Something { ' . "\n" . ' a little bit more complex }', [
            Token::T_TEXT,
        ]);

        $this->tokenTypeTest('text with spaces test', [
            Token::T_TEXT,
        ]);

        $this->tokenTypeTest('testing 1 2 3 @ something random é&é+ - ok', [
            Token::T_TEXT,
        ]);
    }

    public function testFuncCall()
    {
        $this->tokenTypeTest('{{ my_customfunction(  ) }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_SYMBOL,
            Token::T_SYMBOL,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ someFunction( "some string", 5, anotherfunc() ) }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_SYMBOL,
            Token::T_STRING,
            Token::T_SYMBOL,
            Token::T_NUMBER,
            Token::T_SYMBOL,
            Token::T_IDENT,
            Token::T_SYMBOL,
            Token::T_SYMBOL,
            Token::T_SYMBOL,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ myCustomFunction() }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_SYMBOL,
            Token::T_SYMBOL,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ test() }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_SYMBOL,
            Token::T_SYMBOL,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ test("string") }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_SYMBOL,
            Token::T_STRING,
            Token::T_SYMBOL,
            Token::T_CLOSING_TAG,
        ]);
    }

    public function testNumber()
    {
        $this->tokenTypeTest('{{ 5 }}', [
            Token::T_OPENING_TAG,
            Token::T_NUMBER,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ 68}}', [
            Token::T_OPENING_TAG,
            Token::T_NUMBER,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{245 }}', [
            Token::T_OPENING_TAG,
            Token::T_NUMBER,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{100}}', [
            Token::T_OPENING_TAG,
            Token::T_NUMBER,
            Token::T_CLOSING_TAG,
        ]);
    }

    public function testString()
    {
        $this->tokenTypeTest("{{ 'this is a string' }}", [
            Token::T_OPENING_TAG,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest("{{ 'this is a string' }}", [
            Token::T_OPENING_TAG,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ "string...,"}}', [
            Token::T_OPENING_TAG,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
        ]);
    }

    public function testIf()
    {
        $this->tokenTypeTest('{{ if @variable }}{{ @variable }}{{ /if }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
            Token::T_OPENING_TAG,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ if @variable }}{{ "string" }}text{{ /if }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
            Token::T_OPENING_TAG,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
            Token::T_TEXT,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_CLOSING_TAG,
        ]);
    }

    public function testBlock()
    {
        $this->tokenTypeTest('{{ block "string" }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ block "string" }}{{ /block }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ block "something" + @variable + @variable.property + "something" }}{{ raw @variable }}{{ /block }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_STRING,
            Token::T_OP,
            Token::T_VAR,
            Token::T_OP,
            Token::T_VAR,
            Token::T_OP,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ block @variable }}this block has content{{ /block }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
            Token::T_TEXT,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ block "string"+@test_var }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_STRING,
            Token::T_OP,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
        ]);

        $this->tokenTypeTest('{{ block "string" + @test_var }}', [
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_STRING,
            Token::T_OP,
            Token::T_VAR,
            Token::T_CLOSING_TAG,
        ]);
    }

    public function testRaw()
    {
        $this->tokenTypeTest('text with spaces {{ raw "string test " + @variable + "string test" }}', [
            Token::T_TEXT,
            Token::T_OPENING_TAG,
            Token::T_IDENT,
            Token::T_STRING,
            Token::T_OP,
            Token::T_VAR,
            Token::T_OP,
            Token::T_STRING,
            Token::T_CLOSING_TAG,
        ]);
    }

    private function tokenTypeTest($input, $tokens)
    {
        $lexer = new Lexer();
        $stream = $lexer->tokenize($input);

        $this->assertCount(count($tokens), $stream->getTokens(), 'Amount of tokens does not match expected amount');

        foreach ($tokens as $k => $type) {
            $this->assertEquals($type, $stream->getToken($k)->getType(), 'Type of token does not match' . $type);
        }
    }
}