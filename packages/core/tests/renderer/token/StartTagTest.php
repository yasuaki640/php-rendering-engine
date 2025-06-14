<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests\Token;

use MyApp\Core\Renderer\Html\Attribute;
use MyApp\Core\Renderer\Token\StartTag;
use PHPUnit\Framework\TestCase;

class StartTagTest extends TestCase
{
    public function testConstructWithoutAttributes(): void
    {
        $tag = new StartTag('div', false);

        $this->assertEquals('div', $tag->tag);
        $this->assertEquals('div', $tag->getTag());
        $this->assertFalse($tag->selfClosing);
        $this->assertFalse($tag->isSelfClosing());
        $this->assertEmpty($tag->attributes);
        $this->assertEmpty($tag->getAttributes());
    }

    public function testConstructWithSelfClosing(): void
    {
        $tag = new StartTag('img', true);

        $this->assertEquals('img', $tag->getTag());
        $this->assertTrue($tag->isSelfClosing());
        $this->assertEmpty($tag->getAttributes());
    }

    public function testConstructWithAttributes(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
        ];

        $tag = new StartTag('div', false, $attributes);

        $this->assertEquals('div', $tag->getTag());
        $this->assertFalse($tag->isSelfClosing());
        $this->assertCount(2, $tag->getAttributes());
        $this->assertEquals($attributes, $tag->getAttributes());
    }

    public function testGetType(): void
    {
        $tag = new StartTag('p', false);

        $this->assertEquals('StartTag', $tag->getType());
    }

    public function testGetAttribute(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
            new Attribute('data-value', '123'),
        ];

        $tag = new StartTag('div', false, $attributes);

        $idAttr = $tag->getAttribute('id');
        $this->assertNotNull($idAttr);
        $this->assertEquals('id', $idAttr->name);
        $this->assertEquals('test-id', $idAttr->value);

        $classAttr = $tag->getAttribute('class');
        $this->assertNotNull($classAttr);
        $this->assertEquals('class', $classAttr->name);
        $this->assertEquals('test-class', $classAttr->value);

        $dataAttr = $tag->getAttribute('data-value');
        $this->assertNotNull($dataAttr);
        $this->assertEquals('data-value', $dataAttr->name);
        $this->assertEquals('123', $dataAttr->value);
    }

    public function testGetAttributeNotExists(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
        ];

        $tag = new StartTag('div', false, $attributes);

        $this->assertNull($tag->getAttribute('class'));
        $this->assertNull($tag->getAttribute('nonexistent'));
    }

    public function testHasAttribute(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
        ];

        $tag = new StartTag('div', false, $attributes);

        $this->assertTrue($tag->hasAttribute('id'));
        $this->assertTrue($tag->hasAttribute('class'));
        $this->assertFalse($tag->hasAttribute('data-value'));
        $this->assertFalse($tag->hasAttribute('nonexistent'));
    }

    public function testHasAttributeWithEmptyAttributes(): void
    {
        $tag = new StartTag('div', false);

        $this->assertFalse($tag->hasAttribute('id'));
        $this->assertFalse($tag->hasAttribute('class'));
    }

    public function testWithDifferentTagNames(): void
    {
        $tagNames = ['html', 'head', 'body', 'div', 'span', 'h1', 'h2', 'p', 'a', 'img'];

        foreach ($tagNames as $tagName) {
            $tag = new StartTag($tagName, false);
            $this->assertEquals($tagName, $tag->getTag());
            $this->assertEquals('StartTag', $tag->getType());
        }
    }

    public function testWithEmptyTagName(): void
    {
        $tag = new StartTag('', false);

        $this->assertEquals('', $tag->getTag());
        $this->assertEquals('StartTag', $tag->getType());
    }

    public function testWithUppercaseTagName(): void
    {
        $tag = new StartTag('DIV', false);

        $this->assertEquals('DIV', $tag->getTag());
        $this->assertEquals('StartTag', $tag->getType());
    }

    public function testSelfClosingTags(): void
    {
        $selfClosingTags = ['img', 'br', 'hr', 'input', 'meta', 'link'];

        foreach ($selfClosingTags as $tagName) {
            $tag = new StartTag($tagName, true);
            $this->assertTrue($tag->isSelfClosing());
            $this->assertEquals('StartTag', $tag->getType());
        }
    }

    public function testAttributeWithEmptyValues(): void
    {
        $attributes = [
            new Attribute('disabled', ''),
            new Attribute('checked', ''),
        ];

        $tag = new StartTag('input', true, $attributes);

        $disabledAttr = $tag->getAttribute('disabled');
        $this->assertNotNull($disabledAttr);
        $this->assertEquals('', $disabledAttr->value);

        $checkedAttr = $tag->getAttribute('checked');
        $this->assertNotNull($checkedAttr);
        $this->assertEquals('', $checkedAttr->value);
    }

    public function testAttributeNameCaseSensitive(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('ID', 'test-ID'),
        ];

        $tag = new StartTag('div', false, $attributes);

        $this->assertTrue($tag->hasAttribute('id'));
        $this->assertTrue($tag->hasAttribute('ID'));
        $this->assertFalse($tag->hasAttribute('Id'));

        $idAttr = $tag->getAttribute('id');
        $this->assertNotNull($idAttr);
        $this->assertEquals('test-id', $idAttr->value);

        $IDAttr = $tag->getAttribute('ID');
        $this->assertNotNull($IDAttr);
        $this->assertEquals('test-ID', $IDAttr->value);
    }
}
