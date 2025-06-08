<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Tests\Renderer\Css;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Renderer\Css\CssToken;
use Yasuaki640\PhpRenderingEngine\Renderer\Css\CssTokenizer;
use Yasuaki640\PhpRenderingEngine\Renderer\Css\CssTokenType;

class CssTokenizerTest extends TestCase
{
    public function testEmpty(): void
    {
        $style = '';
        $tokenizer = new CssTokenizer($style);
        $tokens = iterator_to_array($tokenizer);
        $this->assertEmpty($tokens);
    }

    public function testOneRule(): void
    {
        $style = 'p { color: red; }';
        $tokenizer = new CssTokenizer($style);
        $expected = [
            CssToken::ident('p'),
            CssToken::openCurly(),
            CssToken::ident('color'),
            CssToken::colon(),
            CssToken::ident('red'),
            CssToken::semiColon(),
            CssToken::closeCurly(),
        ];

        $tokens = iterator_to_array($tokenizer);
        $this->assertCount(count($expected), $tokens);

        foreach ($expected as $i => $expectedToken) {
            $this->assertTrue(
                $expectedToken->equals($tokens[$i]),
                "Token at index {$i} does not match. Expected: {$expectedToken->type->value}, Got: {$tokens[$i]->type->value}"
            );
        }
    }

    public function testIdSelector(): void
    {
        $style = '#id { color: red; }';
        $tokenizer = new CssTokenizer($style);
        $expected = [
            CssToken::hashToken('#id'),
            CssToken::openCurly(),
            CssToken::ident('color'),
            CssToken::colon(),
            CssToken::ident('red'),
            CssToken::semiColon(),
            CssToken::closeCurly(),
        ];

        $tokens = iterator_to_array($tokenizer);
        $this->assertCount(count($expected), $tokens);

        foreach ($expected as $i => $expectedToken) {
            $this->assertTrue(
                $expectedToken->equals($tokens[$i]),
                "Token at index {$i} does not match. Expected: {$expectedToken->type->value}, Got: {$tokens[$i]->type->value}"
            );
        }
    }

    public function testClassSelector(): void
    {
        $style = '.class { color: red; }';
        $tokenizer = new CssTokenizer($style);
        $expected = [
            CssToken::delim('.'),
            CssToken::ident('class'),
            CssToken::openCurly(),
            CssToken::ident('color'),
            CssToken::colon(),
            CssToken::ident('red'),
            CssToken::semiColon(),
            CssToken::closeCurly(),
        ];

        $tokens = iterator_to_array($tokenizer);
        $this->assertCount(count($expected), $tokens);

        foreach ($expected as $i => $expectedToken) {
            $this->assertTrue(
                $expectedToken->equals($tokens[$i]),
                "Token at index {$i} does not match. Expected: {$expectedToken->type->value}, Got: {$tokens[$i]->type->value}"
            );
        }
    }

    public function testMultipleRules(): void
    {
        $style = 'p { content: "Hey"; } h1 { font-size: 40; color: blue; }';
        $tokenizer = new CssTokenizer($style);
        $expected = [
            CssToken::ident('p'),
            CssToken::openCurly(),
            CssToken::ident('content'),
            CssToken::colon(),
            CssToken::stringToken('Hey'),
            CssToken::semiColon(),
            CssToken::closeCurly(),
            CssToken::ident('h1'),
            CssToken::openCurly(),
            CssToken::ident('font-size'),
            CssToken::colon(),
            CssToken::number(40.0),
            CssToken::semiColon(),
            CssToken::ident('color'),
            CssToken::colon(),
            CssToken::ident('blue'),
            CssToken::semiColon(),
            CssToken::closeCurly(),
        ];

        $tokens = iterator_to_array($tokenizer);
        $this->assertCount(count($expected), $tokens);

        foreach ($expected as $i => $expectedToken) {
            $this->assertTrue(
                $expectedToken->equals($tokens[$i]),
                "Token at index {$i} does not match. Expected: {$expectedToken->type->value} with value {$expectedToken->getValue()}, Got: {$tokens[$i]->type->value} with value {$tokens[$i]->getValue()}"
            );
        }
    }

    public function testCssTokenEquality(): void
    {
        $token1 = CssToken::ident('test');
        $token2 = CssToken::ident('test');
        $token3 = CssToken::ident('different');

        $this->assertTrue($token1->equals($token2));
        $this->assertFalse($token1->equals($token3));
    }

    public function testCssTokenTypes(): void
    {
        $hashToken = CssToken::hashToken('#test');
        $this->assertSame(CssTokenType::HashToken, $hashToken->type);
        $this->assertSame('#test', $hashToken->getValue());

        $delimToken = CssToken::delim('.');
        $this->assertSame(CssTokenType::Delim, $delimToken->type);
        $this->assertSame('.', $delimToken->getValue());

        $numberToken = CssToken::number(42.5);
        $this->assertSame(CssTokenType::Number, $numberToken->type);
        $this->assertSame(42.5, $numberToken->getValue());

        $colonToken = CssToken::colon();
        $this->assertSame(CssTokenType::Colon, $colonToken->type);
        $this->assertSame(':', $colonToken->getValue());
    }
}
