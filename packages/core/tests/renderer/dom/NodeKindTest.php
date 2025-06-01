<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests\Dom;

use MyApp\Core\Renderer\Dom\NodeKind;
use PHPUnit\Framework\TestCase;

class NodeKindTest extends TestCase
{
    public function testAllNodeKinds(): void
    {
        $allKinds = NodeKind::cases();

        $this->assertCount(3, $allKinds);
        $this->assertContains(NodeKind::Document, $allKinds);
        $this->assertContains(NodeKind::Element, $allKinds);
        $this->assertContains(NodeKind::Text, $allKinds);
    }

    public function testNodeKindEquality(): void
    {
        $this->assertEquals(NodeKind::Document, NodeKind::Document);
        $this->assertEquals(NodeKind::Element, NodeKind::Element);
        $this->assertEquals(NodeKind::Text, NodeKind::Text);

        $this->assertNotEquals(NodeKind::Document, NodeKind::Element);
        $this->assertNotEquals(NodeKind::Element, NodeKind::Text);
        $this->assertNotEquals(NodeKind::Text, NodeKind::Document);
    }

    public function testNodeKindNames(): void
    {
        $this->assertEquals('Document', NodeKind::Document->name);
        $this->assertEquals('Element', NodeKind::Element->name);
        $this->assertEquals('Text', NodeKind::Text->name);
    }
}
