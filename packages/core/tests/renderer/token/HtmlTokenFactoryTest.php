<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests\Token;

use MyApp\Core\Renderer\Token\Attribute;
use MyApp\Core\Renderer\Token\CharToken;
use MyApp\Core\Renderer\Token\EndTag;
use MyApp\Core\Renderer\Token\EofToken;
use MyApp\Core\Renderer\Token\HtmlTokenFactory;
use MyApp\Core\Renderer\Token\StartTag;
use PHPUnit\Framework\TestCase;

class HtmlTokenFactoryTest extends TestCase
{
    public function testCreateStartTag(): void
    {
        $tag = HtmlTokenFactory::createStartTag('div');

        $this->assertInstanceOf(StartTag::class, $tag);
        $this->assertEquals('div', $tag->getTag());
        $this->assertFalse($tag->isSelfClosing());
        $this->assertEmpty($tag->getAttributes());
        $this->assertEquals('StartTag', $tag->getType());
    }

    public function testCreateStartTagWithSelfClosing(): void
    {
        $tag = HtmlTokenFactory::createStartTag('img', true);

        $this->assertInstanceOf(StartTag::class, $tag);
        $this->assertEquals('img', $tag->getTag());
        $this->assertTrue($tag->isSelfClosing());
        $this->assertEmpty($tag->getAttributes());
        $this->assertEquals('StartTag', $tag->getType());
    }

    public function testCreateStartTagWithAttributes(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
        ];

        $tag = HtmlTokenFactory::createStartTag('div', false, $attributes);

        $this->assertInstanceOf(StartTag::class, $tag);
        $this->assertEquals('div', $tag->getTag());
        $this->assertFalse($tag->isSelfClosing());
        $this->assertCount(2, $tag->getAttributes());
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testCreateStartTagWithSelfClosingAndAttributes(): void
    {
        $attributes = [
            new Attribute('src', 'image.jpg'),
            new Attribute('alt', 'Test image'),
        ];

        $tag = HtmlTokenFactory::createStartTag('img', true, $attributes);

        $this->assertInstanceOf(StartTag::class, $tag);
        $this->assertEquals('img', $tag->getTag());
        $this->assertTrue($tag->isSelfClosing());
        $this->assertCount(2, $tag->getAttributes());
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testCreateEndTag(): void
    {
        $tag = HtmlTokenFactory::createEndTag('div');

        $this->assertInstanceOf(EndTag::class, $tag);
        $this->assertEquals('div', $tag->getTag());
        $this->assertEquals('EndTag', $tag->getType());
    }

    public function testCreateEndTagWithDifferentNames(): void
    {
        $tagNames = ['html', 'head', 'body', 'div', 'span', 'h1', 'h2', 'p'];

        foreach ($tagNames as $tagName) {
            $tag = HtmlTokenFactory::createEndTag($tagName);
            $this->assertInstanceOf(EndTag::class, $tag);
            $this->assertEquals($tagName, $tag->getTag());
            $this->assertEquals('EndTag', $tag->getType());
        }
    }

    public function testCreateChar(): void
    {
        $token = HtmlTokenFactory::createChar('a');

        $this->assertInstanceOf(CharToken::class, $token);
        $this->assertEquals('a', $token->getChar());
        $this->assertEquals('Char', $token->getType());
    }

    public function testCreateCharWithSpecialCharacters(): void
    {
        $specialChars = [' ', '\n', '\t', '<', '>', '&', '"', "'"];

        foreach ($specialChars as $char) {
            $token = HtmlTokenFactory::createChar($char);
            $this->assertInstanceOf(CharToken::class, $token);
            $this->assertEquals($char, $token->getChar());
            $this->assertEquals('Char', $token->getType());
        }
    }

    public function testCreateCharWithMultibyteCharacter(): void
    {
        $token = HtmlTokenFactory::createChar('あ');

        $this->assertInstanceOf(CharToken::class, $token);
        $this->assertEquals('あ', $token->getChar());
        $this->assertEquals('Char', $token->getType());
    }

    public function testCreateEof(): void
    {
        $token = HtmlTokenFactory::createEof();

        $this->assertInstanceOf(EofToken::class, $token);
        $this->assertEquals('Eof', $token->getType());
    }

    public function testCreateMultipleEofTokens(): void
    {
        $token1 = HtmlTokenFactory::createEof();
        $token2 = HtmlTokenFactory::createEof();

        $this->assertInstanceOf(EofToken::class, $token1);
        $this->assertInstanceOf(EofToken::class, $token2);
        $this->assertEquals($token1->getType(), $token2->getType());
        $this->assertNotSame($token1, $token2);
    }

    public function testCreateAttribute(): void
    {
        $attribute = HtmlTokenFactory::createAttribute('id', 'test-value');

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals('id', $attribute->name);
        $this->assertEquals('test-value', $attribute->value);
    }

    public function testCreateAttributeWithEmptyValues(): void
    {
        $attribute = HtmlTokenFactory::createAttribute('', '');

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals('', $attribute->name);
        $this->assertEquals('', $attribute->value);
    }

    public function testCreateAttributeWithSpecialCharacters(): void
    {
        $attribute = HtmlTokenFactory::createAttribute('data-test', 'value with spaces & symbols');

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals('data-test', $attribute->name);
        $this->assertEquals('value with spaces & symbols', $attribute->value);
    }

    public function testFactoryMethodsReturnDifferentInstances(): void
    {
        $tag1 = HtmlTokenFactory::createStartTag('div');
        $tag2 = HtmlTokenFactory::createStartTag('div');

        $this->assertNotSame($tag1, $tag2);
        $this->assertEquals($tag1->getTag(), $tag2->getTag());

        $endTag1 = HtmlTokenFactory::createEndTag('div');
        $endTag2 = HtmlTokenFactory::createEndTag('div');

        $this->assertNotSame($endTag1, $endTag2);
        $this->assertEquals($endTag1->getTag(), $endTag2->getTag());

        $char1 = HtmlTokenFactory::createChar('a');
        $char2 = HtmlTokenFactory::createChar('a');

        $this->assertNotSame($char1, $char2);
        $this->assertEquals($char1->getChar(), $char2->getChar());
    }

    public function testComplexTokenCreation(): void
    {
        // 複雑なStartTagの作成テスト
        $attributes = [
            HtmlTokenFactory::createAttribute('id', 'main-content'),
            HtmlTokenFactory::createAttribute('class', 'container fluid'),
            HtmlTokenFactory::createAttribute('data-toggle', 'modal'),
            HtmlTokenFactory::createAttribute('data-target', '#myModal'),
        ];

        $tag = HtmlTokenFactory::createStartTag('div', false, $attributes);

        $this->assertInstanceOf(StartTag::class, $tag);
        $this->assertEquals('div', $tag->getTag());
        $this->assertFalse($tag->isSelfClosing());
        $this->assertCount(4, $tag->getAttributes());

        $this->assertTrue($tag->hasAttribute('id'));
        $this->assertTrue($tag->hasAttribute('class'));
        $this->assertTrue($tag->hasAttribute('data-toggle'));
        $this->assertTrue($tag->hasAttribute('data-target'));

        $idAttr = $tag->getAttribute('id');
        $this->assertEquals('main-content', $idAttr->value);
    }
}
