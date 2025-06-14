<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Dom;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\ElementKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\Attribute;

class ElementTest extends TestCase
{
    public function testCreateElement(): void
    {
        $element = new Element('div');

        $this->assertEquals(ElementKind::Div, $element->getKind());
        $this->assertEmpty($element->getAttributes());
    }

    public function testCreateElementWithAttributes(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
        ];

        $element = new Element('p', $attributes);

        $this->assertEquals(ElementKind::P, $element->getKind());
        $this->assertCount(2, $element->getAttributes());
        $this->assertEquals($attributes, $element->getAttributes());
    }

    public function testGetAttribute(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
        ];

        $element = new Element('div', $attributes);

        $idAttr = $element->getAttribute('id');
        $this->assertNotNull($idAttr);
        $this->assertEquals('id', $idAttr->name);
        $this->assertEquals('test-id', $idAttr->value);

        $this->assertNull($element->getAttribute('nonexistent'));
    }

    public function testHasAttribute(): void
    {
        $attributes = [
            new Attribute('id', 'test-id'),
        ];

        $element = new Element('div', $attributes);

        $this->assertTrue($element->hasAttribute('id'));
        $this->assertFalse($element->hasAttribute('class'));
    }

    public function testAddAttribute(): void
    {
        $element = new Element('div');
        $attribute = new Attribute('data-test', 'value');

        $element->addAttribute($attribute);

        $this->assertCount(1, $element->getAttributes());
        $this->assertTrue($element->hasAttribute('data-test'));
    }

    public function testSetAttributes(): void
    {
        $element = new Element('div');
        $attributes = [
            new Attribute('id', 'test-id'),
            new Attribute('class', 'test-class'),
        ];

        $element->setAttributes($attributes);

        $this->assertCount(2, $element->getAttributes());
        $this->assertEquals($attributes, $element->getAttributes());
    }

    public function testInvalidElementName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unimplemented element name: unknown');

        new Element('unknown');
    }
}
