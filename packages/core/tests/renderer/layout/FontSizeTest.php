<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\FontSize;

class FontSizeTest extends TestCase
{
    public function testDefaultForNullNode(): void
    {
        $fontSize = FontSize::defaultForNode(null);
        $this->assertEquals(FontSize::Medium, $fontSize);
    }

    public function testDefaultForH1Node(): void
    {
        $element = new Element('h1');
        $node = new Node(NodeKind::Element, $element);

        $fontSize = FontSize::defaultForNode($node);
        $this->assertEquals(FontSize::XXLarge, $fontSize);
    }

    public function testDefaultForH2Node(): void
    {
        $element = new Element('h2');
        $node = new Node(NodeKind::Element, $element);

        $fontSize = FontSize::defaultForNode($node);
        $this->assertEquals(FontSize::XLarge, $fontSize);
    }

    public function testDefaultForOtherNode(): void
    {
        $element = new Element('p');
        $node = new Node(NodeKind::Element, $element);

        $fontSize = FontSize::defaultForNode($node);
        $this->assertEquals(FontSize::Medium, $fontSize);
    }

    public function testEnumCases(): void
    {
        $this->assertEquals('Medium', FontSize::Medium->name);
        $this->assertEquals('XLarge', FontSize::XLarge->name);
        $this->assertEquals('XXLarge', FontSize::XXLarge->name);
    }
}
