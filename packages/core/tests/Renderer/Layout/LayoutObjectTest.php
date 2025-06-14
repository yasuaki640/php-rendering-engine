<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\ComputedStyle;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\DisplayType;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutObject;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutObjectKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutPoint;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutSize;

class LayoutObjectTest extends TestCase
{
    public function testConstruct(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $this->assertEquals(LayoutObjectKind::Block, $layoutObject->getKind());
        $this->assertInstanceOf(ComputedStyle::class, $layoutObject->getStyle());
        $this->assertInstanceOf(LayoutPoint::class, $layoutObject->getPoint());
        $this->assertInstanceOf(LayoutSize::class, $layoutObject->getSize());
        $this->assertNull($layoutObject->getFirstChild());
        $this->assertNull($layoutObject->getNextSibling());
        $this->assertNull($layoutObject->getParent());
    }

    public function testConstructWithParent(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $parentElement = new Element('body', []);
        $parentNode = new Node(NodeKind::Element, $parentElement);
        $parent = new LayoutObject($parentNode);

        $layoutObject = new LayoutObject($elementNode, $parent);

        $this->assertSame($parent, $layoutObject->getParent());
    }

    public function testSettersAndGetters(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $childElement = new Element('p', []);
        $childNode = new Node(NodeKind::Element, $childElement);
        $child = new LayoutObject($childNode);
        $layoutObject->setFirstChild($child);
        $this->assertSame($child, $layoutObject->getFirstChild());

        $siblingElement = new Element('span', []);
        $siblingNode = new Node(NodeKind::Element, $siblingElement);
        $sibling = new LayoutObject($siblingNode);
        $layoutObject->setNextSibling($sibling);
        $this->assertSame($sibling, $layoutObject->getNextSibling());
    }

    public function testPaintWithDisplayNone(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        // DisplayNoneに設定
        $style = $layoutObject->getStyle();
        $style->setDisplay(DisplayType::DisplayNone);

        $items = $layoutObject->paint();
        $this->assertEmpty($items);
    }

    public function testComputeSize(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $parentSize = new LayoutSize(800, 600);
        $layoutObject->computeSize($parentSize);

        // ブロック要素の場合、親の幅を継承
        $this->assertEquals(800, $layoutObject->getSize()->getWidth());
    }

    public function testComputePosition(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $parentPoint = new LayoutPoint(10, 20);
        $layoutObject->computePosition(
            $parentPoint,
            LayoutObjectKind::Block,
            null,
            null
        );

        // ブロック要素の場合、親のX座標を継承、Y座標も親と同じ（前の兄弟がない場合）
        $this->assertEquals(10, $layoutObject->getPoint()->getX());
        $this->assertEquals(20, $layoutObject->getPoint()->getY());
    }

    public function testComputePositionWithPreviousSibling(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $parentPoint = new LayoutPoint(10, 20);
        $previousPoint = new LayoutPoint(10, 30);
        $previousSize = new LayoutSize(100, 50);

        $layoutObject->computePosition(
            $parentPoint,
            LayoutObjectKind::Block,
            $previousPoint,
            $previousSize
        );

        // 前の兄弟がブロック要素の場合、Y座標は前の兄弟の下に配置
        $this->assertEquals(10, $layoutObject->getPoint()->getX());
        $this->assertEquals(80, $layoutObject->getPoint()->getY()); // 30 + 50
    }

    public function testCascadingStyle(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $declarations = [
            ['property' => 'background-color', 'value' => 'red'],
            ['property' => 'color', 'value' => '#0000ff'],
            ['property' => 'display', 'value' => 'inline'],
        ];

        $layoutObject->cascadingStyle($declarations);

        // スタイルが適用されているかは実際のdefaulting後に確認する必要がある
        // ここでは例外が発生しないことを確認
        $this->assertTrue(true);
    }

    public function testDefaultingStyle(): void
    {
        $element = new Element('div', []);
        $elementNode = new Node(NodeKind::Element, $element);
        $layoutObject = new LayoutObject($elementNode);

        $layoutObject->defaultingStyle(null, null);

        // デフォルト値が設定される
        $this->assertInstanceOf(ComputedStyle::class, $layoutObject->getStyle());
    }

    public function testUpdateKindForTextNode(): void
    {
        $textNode = new Node(NodeKind::Text, 'Hello World');

        $layoutObject = new LayoutObject($textNode);
        $layoutObject->defaultingStyle(null, null);
        $layoutObject->updateKind();

        $this->assertEquals(LayoutObjectKind::Text, $layoutObject->getKind());
    }

    public function testUpdateKindThrowsExceptionForDocument(): void
    {
        $document = new Node(NodeKind::Document);

        $layoutObject = new LayoutObject($document);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('should not create a layout object for a Document node');
        $layoutObject->updateKind();
    }
}
