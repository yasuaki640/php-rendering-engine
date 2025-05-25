<?php

declare(strict_types=1);

namespace MyApp\Core\Tests;

use MyApp\Core\Renderer\Attribute;
use MyApp\Core\Renderer\Token\CharToken;
use MyApp\Core\Renderer\Token\EndTag;
use MyApp\Core\Renderer\Token\EofToken;
use MyApp\Core\Renderer\Token\HtmlTokenFactory;
use MyApp\Core\Renderer\Token\StartTag;
use PHPUnit\Framework\TestCase;

class HtmlTokenTest extends TestCase
{
    public function testStartTagCreation(): void
    {
        $attributes = [
            new Attribute('class', 'container'),
            new Attribute('id', 'main')
        ];
        
        $startTag = new StartTag('div', false, $attributes);
        
        $this->assertEquals('StartTag', $startTag->getType());
        $this->assertEquals('div', $startTag->getTag());
        $this->assertFalse($startTag->isSelfClosing());
        $this->assertCount(2, $startTag->getAttributes());
        $this->assertTrue($startTag->hasAttribute('class'));
        $this->assertTrue($startTag->hasAttribute('id'));
        $this->assertFalse($startTag->hasAttribute('data-test'));
        
        $classAttr = $startTag->getAttribute('class');
        $this->assertNotNull($classAttr);
        $this->assertEquals('container', $classAttr->getValue());
    }

    public function testSelfClosingStartTag(): void
    {
        $startTag = new StartTag('img', true, [
            new Attribute('src', 'test.jpg'),
            new Attribute('alt', 'Test Image')
        ]);
        
        $this->assertTrue($startTag->isSelfClosing());
        $this->assertEquals('img', $startTag->getTag());
    }

    public function testEndTagCreation(): void
    {
        $endTag = new EndTag('div');
        
        $this->assertEquals('EndTag', $endTag->getType());
        $this->assertEquals('div', $endTag->getTag());
    }

    public function testCharTokenCreation(): void
    {
        $charToken = new CharToken('a');
        
        $this->assertEquals('Char', $charToken->getType());
        $this->assertEquals('a', $charToken->getChar());
    }

    public function testEofTokenCreation(): void
    {
        $eofToken = new EofToken();
        
        $this->assertEquals('Eof', $eofToken->getType());
    }

    public function testAttributeCreation(): void
    {
        $attribute = new Attribute('href', 'https://example.com');
        
        $this->assertEquals('href', $attribute->getName());
        $this->assertEquals('https://example.com', $attribute->getValue());
        $this->assertEquals('href="https://example.com"', (string)$attribute);
    }

    public function testHtmlTokenFactory(): void
    {
        $startTag = HtmlTokenFactory::createStartTag('p', false, [
            HtmlTokenFactory::createAttribute('class', 'text')
        ]);
        $endTag = HtmlTokenFactory::createEndTag('p');
        $charToken = HtmlTokenFactory::createChar('H');
        $eofToken = HtmlTokenFactory::createEof();

        $this->assertInstanceOf(StartTag::class, $startTag);
        $this->assertInstanceOf(EndTag::class, $endTag);
        $this->assertInstanceOf(CharToken::class, $charToken);
        $this->assertInstanceOf(EofToken::class, $eofToken);
        
        $this->assertEquals('p', $startTag->getTag());
        $this->assertEquals('p', $endTag->getTag());
        $this->assertEquals('H', $charToken->getChar());
    }
}
