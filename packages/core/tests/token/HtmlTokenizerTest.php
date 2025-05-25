<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Token;

use MyApp\Core\Renderer\Token\CharToken;
use MyApp\Core\Renderer\Token\EndTag;
use MyApp\Core\Renderer\Token\HtmlTokenizer;
use MyApp\Core\Renderer\Token\StartTag;
use PHPUnit\Framework\TestCase;

class HtmlTokenizerTest extends TestCase
{
    public function testEmpty(): void
    {
        $html = '';
        $tokenizer = new HtmlTokenizer($html);

        $this->assertNull($tokenizer->next());
    }

    public function testStartAndEndTag(): void
    {
        $html = '<body></body>';
        $tokenizer = new HtmlTokenizer($html);

        $expected = [
            new StartTag('body', false, []),
            new EndTag('body'),
        ];

        foreach ($expected as $expectedToken) {
            $actualToken = $tokenizer->next();
            $this->assertNotNull($actualToken);
            $this->assertEquals($expectedToken->getType(), $actualToken->getType());
            $this->assertEquals($expectedToken->getTag(), $actualToken->getTag());

            if ($expectedToken instanceof StartTag) {
                $this->assertEquals($expectedToken->isSelfClosing(), $actualToken->isSelfClosing());
                $this->assertEquals($expectedToken->getAttributes(), $actualToken->getAttributes());
            }
        }

        $this->assertNull($tokenizer->next());
    }

    public function testAttributes(): void
    {
        $html = '<p class="A" id=\'B\' foo=bar></p>';
        $tokenizer = new HtmlTokenizer($html);

        $startTagToken = $tokenizer->next();
        $this->assertNotNull($startTagToken);
        $this->assertInstanceOf(StartTag::class, $startTagToken);
        $this->assertEquals('p', $startTagToken->getTag());
        $this->assertFalse($startTagToken->isSelfClosing());
        $this->assertCount(3, $startTagToken->getAttributes());

        $actualAttrs = $startTagToken->getAttributes();

        // 実際の属性の値を確認
        $this->assertEquals('class', $actualAttrs[0]->name);
        $this->assertEquals('A', $actualAttrs[0]->value);

        $this->assertEquals('id', $actualAttrs[1]->name);
        $this->assertEquals('B', $actualAttrs[1]->value);

        $this->assertEquals('foo', $actualAttrs[2]->name);
        $this->assertEquals('bar', $actualAttrs[2]->value);

        $endTagToken = $tokenizer->next();
        $this->assertNotNull($endTagToken);
        $this->assertInstanceOf(EndTag::class, $endTagToken);
        $this->assertEquals('p', $endTagToken->getTag());

        $this->assertNull($tokenizer->next());
    }

    public function testSelfClosingTag(): void
    {
        $html = '<img />';
        $tokenizer = new HtmlTokenizer($html);

        $expected = [
            new StartTag('img', true, []),
        ];

        foreach ($expected as $expectedToken) {
            $actualToken = $tokenizer->next();
            $this->assertNotNull($actualToken);
            $this->assertEquals($expectedToken->getType(), $actualToken->getType());
            $this->assertEquals($expectedToken->getTag(), $actualToken->getTag());
            $this->assertEquals($expectedToken->isSelfClosing(), $actualToken->isSelfClosing());
            $this->assertEquals($expectedToken->getAttributes(), $actualToken->getAttributes());
        }

        $this->assertNull($tokenizer->next());
    }

    public function testScriptTag(): void
    {
        $html = '<script>js code;</script>';
        $tokenizer = new HtmlTokenizer($html);

        $expected = [
            new StartTag('script', false, []),
            new CharToken('j'),
            new CharToken('s'),
            new CharToken(' '),
            new CharToken('c'),
            new CharToken('o'),
            new CharToken('d'),
            new CharToken('e'),
            new CharToken(';'),
            new EndTag('script'),
        ];

        foreach ($expected as $expectedToken) {
            $actualToken = $tokenizer->next();
            $this->assertNotNull($actualToken);
            $this->assertEquals($expectedToken->getType(), $actualToken->getType());

            if ($expectedToken instanceof CharToken) {
                $this->assertEquals($expectedToken->getChar(), $actualToken->getChar());
            } elseif ($expectedToken instanceof StartTag || $expectedToken instanceof EndTag) {
                $this->assertEquals($expectedToken->getTag(), $actualToken->getTag());

                if ($expectedToken instanceof StartTag) {
                    $this->assertEquals($expectedToken->isSelfClosing(), $actualToken->isSelfClosing());
                    $this->assertEquals($expectedToken->getAttributes(), $actualToken->getAttributes());
                }
            }
        }

        $this->assertNull($tokenizer->next());
    }

    public function testMultipleTags(): void
    {
        $html = '<html><head><title>Test</title></head><body><h1>Hello</h1></body></html>';
        $tokenizer = new HtmlTokenizer($html);

        $expected = [
            new StartTag('html', false, []),
            new StartTag('head', false, []),
            new StartTag('title', false, []),
            new CharToken('T'),
            new CharToken('e'),
            new CharToken('s'),
            new CharToken('t'),
            new EndTag('title'),
            new EndTag('head'),
            new StartTag('body', false, []),
            new StartTag('h1', false, []),
            new CharToken('H'),
            new CharToken('e'),
            new CharToken('l'),
            new CharToken('l'),
            new CharToken('o'),
            new EndTag('h1'),
            new EndTag('body'),
            new EndTag('html'),
        ];

        foreach ($expected as $expectedToken) {
            $actualToken = $tokenizer->next();
            $this->assertNotNull($actualToken);
            $this->assertEquals($expectedToken->getType(), $actualToken->getType());

            if ($expectedToken instanceof CharToken) {
                $this->assertEquals($expectedToken->getChar(), $actualToken->getChar());
            } elseif ($expectedToken instanceof StartTag || $expectedToken instanceof EndTag) {
                $this->assertEquals($expectedToken->getTag(), $actualToken->getTag());

                if ($expectedToken instanceof StartTag) {
                    $this->assertEquals($expectedToken->isSelfClosing(), $actualToken->isSelfClosing());
                    $this->assertEquals($expectedToken->getAttributes(), $actualToken->getAttributes());
                }
            }
        }

        $this->assertNull($tokenizer->next());
    }

    public function testSpecialCharacters(): void
    {
        $html = '<div>Hello &amp; World!</div>';
        $tokenizer = new HtmlTokenizer($html);

        $expected = [
            new StartTag('div', false, []),
            new CharToken('H'),
            new CharToken('e'),
            new CharToken('l'),
            new CharToken('l'),
            new CharToken('o'),
            new CharToken(' '),
            new CharToken('&'),
            new CharToken('a'),
            new CharToken('m'),
            new CharToken('p'),
            new CharToken(';'),
            new CharToken(' '),
            new CharToken('W'),
            new CharToken('o'),
            new CharToken('r'),
            new CharToken('l'),
            new CharToken('d'),
            new CharToken('!'),
            new EndTag('div'),
        ];

        foreach ($expected as $expectedToken) {
            $actualToken = $tokenizer->next();
            $this->assertNotNull($actualToken);
            $this->assertEquals($expectedToken->getType(), $actualToken->getType());

            if ($expectedToken instanceof CharToken) {
                $this->assertEquals($expectedToken->getChar(), $actualToken->getChar());
            } elseif ($expectedToken instanceof StartTag || $expectedToken instanceof EndTag) {
                $this->assertEquals($expectedToken->getTag(), $actualToken->getTag());

                if ($expectedToken instanceof StartTag) {
                    $this->assertEquals($expectedToken->isSelfClosing(), $actualToken->isSelfClosing());
                    $this->assertEquals($expectedToken->getAttributes(), $actualToken->getAttributes());
                }
            }
        }

        $this->assertNull($tokenizer->next());
    }

    public function testAttributesWithDifferentQuoteStyles(): void
    {
        $html = '<input type="text" placeholder=\'Enter name\' required>';
        $tokenizer = new HtmlTokenizer($html);

        $actualToken = $tokenizer->next();
        $this->assertNotNull($actualToken);
        $this->assertInstanceOf(StartTag::class, $actualToken);
        $this->assertEquals('input', $actualToken->getTag());
        $this->assertFalse($actualToken->isSelfClosing());

        $actualAttrs = $actualToken->getAttributes();
        $this->assertCount(3, $actualAttrs);

        $this->assertEquals('type', $actualAttrs[0]->name);
        $this->assertEquals('text', $actualAttrs[0]->value);

        $this->assertEquals('placeholder', $actualAttrs[1]->name);
        $this->assertEquals('Enter name', $actualAttrs[1]->value);

        $this->assertEquals('required', $actualAttrs[2]->name);
        $this->assertEquals('', $actualAttrs[2]->value);

        $this->assertNull($tokenizer->next());
    }
}
