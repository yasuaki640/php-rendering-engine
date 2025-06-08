<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Renderer\Layout;

use MyApp\Core\Renderer\Layout\LayoutView;
use MyApp\Core\Renderer\Layout\LayoutObjectKind;
use MyApp\Core\Renderer\Dom\NodeKind;
use PHPUnit\Framework\TestCase;

class LayoutViewTest extends TestCase
{
    public function testEmpty(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNull($root);
    }

    public function testBody(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><p>Hello World</p></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        $this->assertEquals(LayoutObjectKind::Block, $root->getKind());
        $this->assertEquals(NodeKind::Element, $root->getNodeKind());
    }

    public function testText(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><p>Hello World</p></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        
        // p要素の確認
        $firstChild = $root->getFirstChild();
        $this->assertNotNull($firstChild);
        $this->assertEquals(LayoutObjectKind::Block, $firstChild->getKind());
        $this->assertEquals(NodeKind::Element, $firstChild->getNodeKind());
        
        // テキストノードの確認
        $textNode = $firstChild->getFirstChild();
        $this->assertNotNull($textNode);
        $this->assertEquals(LayoutObjectKind::Text, $textNode->getKind());
        $this->assertEquals(NodeKind::Text, $textNode->getNodeKind());
    }

    public function testDisplayNone(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><p style="display: none;">Hidden</p></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        // display: noneが適用されているp要素は除外されるので、rootはnullまたは空であるべき
        $this->assertNull($root);
    }

    public function testHiddenClass(): void
    {
        $html = '<!DOCTYPE html><html><head><style>.hidden { display: none; }</style></head><body><p class="hidden">Hidden</p></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        // .hiddenクラスによってdisplay: noneが適用されているp要素は除外されるので、rootはnullまたは空であるべき
        $this->assertNull($root);
    }

    public function testMultipleElements(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><p>First</p><div>Second</div></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        
        // 最初の子要素（p要素）の確認
        $firstChild = $root->getFirstChild();
        $this->assertNotNull($firstChild);
        $this->assertEquals(LayoutObjectKind::Block, $firstChild->getKind());
        
        // 次の兄弟要素（div要素）の確認
        $nextSibling = $firstChild->getNextSibling();
        $this->assertNotNull($nextSibling);
        $this->assertEquals(LayoutObjectKind::Block, $nextSibling->getKind());
    }

    public function testInlineElements(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><span>Inline text</span></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        
        // span要素（インライン要素）の確認
        $firstChild = $root->getFirstChild();
        $this->assertNotNull($firstChild);
        $this->assertEquals(LayoutObjectKind::Inline, $firstChild->getKind());
        $this->assertEquals(NodeKind::Element, $firstChild->getNodeKind());
    }

    public function testNestedElements(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><div><p>Nested paragraph</p></div></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        
        // div要素の確認
        $divElement = $root->getFirstChild();
        $this->assertNotNull($divElement);
        $this->assertEquals(LayoutObjectKind::Block, $divElement->getKind());
        
        // ネストされたp要素の確認
        $nestedP = $divElement->getFirstChild();
        $this->assertNotNull($nestedP);
        $this->assertEquals(LayoutObjectKind::Block, $nestedP->getKind());
        
        // テキストノードの確認
        $textNode = $nestedP->getFirstChild();
        $this->assertNotNull($textNode);
        $this->assertEquals(LayoutObjectKind::Text, $textNode->getKind());
    }

    public function testMixedContent(): void
    {
        $html = '<!DOCTYPE html><html><head></head><body><p>Start <span>inline</span> end</p></body></html>';
        $layoutView = LayoutView::createLayoutView($html);
        
        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        
        // p要素の確認
        $pElement = $root->getFirstChild();
        $this->assertNotNull($pElement);
        $this->assertEquals(LayoutObjectKind::Block, $pElement->getKind());
        
        // 最初のテキストノード "Start "の確認
        $firstText = $pElement->getFirstChild();
        $this->assertNotNull($firstText);
        $this->assertEquals(LayoutObjectKind::Text, $firstText->getKind());
        
        // span要素の確認
        $spanElement = $firstText->getNextSibling();
        $this->assertNotNull($spanElement);
        $this->assertEquals(LayoutObjectKind::Inline, $spanElement->getKind());
    }
}