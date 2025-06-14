<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Exception\UnexpectedInputException;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\DisplayType;

class DisplayTypeTest extends TestCase
{
    public function testFromString(): void
    {
        $this->assertEquals(DisplayType::Block, DisplayType::fromString('block'));
        $this->assertEquals(DisplayType::Inline, DisplayType::fromString('inline'));
        $this->assertEquals(DisplayType::DisplayNone, DisplayType::fromString('none'));
    }

    public function testFromStringUnsupported(): void
    {
        $this->expectException(UnexpectedInputException::class);
        DisplayType::fromString('unsupported');
    }

    public function testDefaultForNullNode(): void
    {
        $displayType = DisplayType::defaultForNode(null);
        $this->assertEquals(DisplayType::Block, $displayType);
    }

    public function testDefaultForDocumentNode(): void
    {
        $node = new Node(NodeKind::Document);

        $displayType = DisplayType::defaultForNode($node);
        $this->assertEquals(DisplayType::Block, $displayType);
    }

    public function testDefaultForTextNode(): void
    {
        $node = new Node(NodeKind::Text, 'sample text');

        $displayType = DisplayType::defaultForNode($node);
        $this->assertEquals(DisplayType::Inline, $displayType);
    }

    public function testDefaultForBlockElement(): void
    {
        $element = new Element('body');
        $node = new Node(NodeKind::Element, $element);

        $displayType = DisplayType::defaultForNode($node);
        $this->assertEquals(DisplayType::Block, $displayType);
    }

    public function testDefaultForInlineElement(): void
    {
        $element = new Element('a');
        $node = new Node(NodeKind::Element, $element);

        $displayType = DisplayType::defaultForNode($node);
        $this->assertEquals(DisplayType::Inline, $displayType);
    }

    public function testEnumCases(): void
    {
        $this->assertEquals('Block', DisplayType::Block->name);
        $this->assertEquals('Inline', DisplayType::Inline->name);
        $this->assertEquals('DisplayNone', DisplayType::DisplayNone->name);
    }
}
