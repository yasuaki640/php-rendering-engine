<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\TextDecoration;

class TextDecorationTest extends TestCase
{
    public function testDefaultForNullNode(): void
    {
        $textDecoration = TextDecoration::defaultForNode(null);
        $this->assertEquals(TextDecoration::None, $textDecoration);
    }

    public function testDefaultForAnchorNode(): void
    {
        $element = new Element('a');
        $node = new Node(NodeKind::Element, $element);

        $textDecoration = TextDecoration::defaultForNode($node);
        $this->assertEquals(TextDecoration::Underline, $textDecoration);
    }

    public function testDefaultForOtherNode(): void
    {
        $element = new Element('p');
        $node = new Node(NodeKind::Element, $element);

        $textDecoration = TextDecoration::defaultForNode($node);
        $this->assertEquals(TextDecoration::None, $textDecoration);
    }

    public function testEnumCases(): void
    {
        $this->assertEquals('None', TextDecoration::None->name);
        $this->assertEquals('Underline', TextDecoration::Underline->name);
    }
}
