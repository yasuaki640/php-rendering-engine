<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Tests\Dom;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\ElementKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Window;

class NodeTest extends TestCase
{
    public function testCreateDocumentNode(): void
    {
        $node = new Node(NodeKind::Document);

        $this->assertEquals(NodeKind::Document, $node->getKind());
        $this->assertNull($node->getElement());
        $this->assertNull($node->getTextContent());
    }

    public function testCreateElementNode(): void
    {
        $element = new Element('div');
        $node = new Node(NodeKind::Element, $element);

        $this->assertEquals(NodeKind::Element, $node->getKind());
        $this->assertSame($element, $node->getElement());
        $this->assertEquals(ElementKind::Div, $node->getElementKind());
        $this->assertNull($node->getTextContent());
    }

    public function testCreateTextNode(): void
    {
        $text = 'Hello, World!';
        $node = new Node(NodeKind::Text, $text);

        $this->assertEquals(NodeKind::Text, $node->getKind());
        $this->assertEquals($text, $node->getTextContent());
        $this->assertNull($node->getElement());
    }

    public function testParentChildRelationship(): void
    {
        $parent = new Node(NodeKind::Element, new Element('div'));
        $child = new Node(NodeKind::Text, 'Hello');

        $parent->appendChild($child);

        $this->assertSame($parent, $child->getParent());
        $this->assertSame($child, $parent->getFirstChild());
        $this->assertSame($child, $parent->getLastChild());
    }

    public function testMultipleChildren(): void
    {
        $parent = new Node(NodeKind::Element, new Element('div'));
        $child1 = new Node(NodeKind::Text, 'First');
        $child2 = new Node(NodeKind::Text, 'Second');
        $child3 = new Node(NodeKind::Text, 'Third');

        $parent->appendChild($child1);
        $parent->appendChild($child2);
        $parent->appendChild($child3);

        $this->assertSame($child1, $parent->getFirstChild());
        $this->assertSame($child3, $parent->getLastChild());

        // 兄弟関係のテスト
        $this->assertNull($child1->getPreviousSibling());
        $this->assertSame($child2, $child1->getNextSibling());

        $this->assertSame($child1, $child2->getPreviousSibling());
        $this->assertSame($child3, $child2->getNextSibling());

        $this->assertSame($child2, $child3->getPreviousSibling());
        $this->assertNull($child3->getNextSibling());
    }

    public function testRemoveChild(): void
    {
        $parent = new Node(NodeKind::Element, new Element('div'));
        $child1 = new Node(NodeKind::Text, 'First');
        $child2 = new Node(NodeKind::Text, 'Second');

        $parent->appendChild($child1);
        $parent->appendChild($child2);
        $parent->removeChild($child1);

        $this->assertNull($child1->getParent());
        $this->assertSame($child2, $parent->getFirstChild());
        $this->assertSame($child2, $parent->getLastChild());
        $this->assertNull($child2->getPreviousSibling());
    }

    public function testWindowReference(): void
    {
        $window = new Window();
        $node = new Node(NodeKind::Element, new Element('div'));

        $node->setWindow($window);

        $this->assertSame($window, $node->getWindow());
    }

    public function testNodeEquality(): void
    {
        $node1 = new Node(NodeKind::Text, 'Hello');
        $node2 = new Node(NodeKind::Text, 'Hello');
        $node3 = new Node(NodeKind::Text, 'World');

        $this->assertTrue($node1->equals($node2));
        $this->assertFalse($node1->equals($node3));
    }

    public function testSetTextContentOnNonTextNode(): void
    {
        $node = new Node(NodeKind::Element, new Element('div'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Text content can only be set on Text nodes');

        $node->setTextContent('This should fail');
    }

    public function testRemoveNonChildNode(): void
    {
        $parent = new Node(NodeKind::Element, new Element('div'));
        $notChild = new Node(NodeKind::Text, 'Not a child');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Node is not a child of this node');

        $parent->removeChild($notChild);
    }
}
