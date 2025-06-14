<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\ElementKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutObjectKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutView;

class LayoutViewTest extends TestCase
{
    /**
     * HTMLからLayoutViewを作成するファクトリーメソッド
     * Rustのcreate_layout_view関数に相当
     */
    private static function createLayoutView(string $html): LayoutView
    {
        return LayoutView::createLayoutView($html);
    }
    public function testEmpty(): void
    {
        $layoutView = self::createLayoutView("");

        $root = $layoutView->getRoot();
        $this->assertNull($root);
    }

    public function testBody(): void
    {
        $html = "<html><head></head><body></body></html>";
        $layoutView = self::createLayoutView($html);

        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        $this->assertEquals(LayoutObjectKind::Block, $root->getKind());

        // PHP版では、getNodeKind()はElementオブジェクトを直接返す
        $nodeKind = $root->getNodeKind();
        $this->assertInstanceOf(Element::class, $nodeKind);
        $this->assertEquals(ElementKind::Body, $nodeKind->getKind());
    }

    public function testText(): void
    {
        $html = "<html><head></head><body>text</body></html>";
        $layoutView = self::createLayoutView($html);

        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        $this->assertEquals(LayoutObjectKind::Block, $root->getKind());

        // PHP版では、getNodeKind()はElementオブジェクトを直接返す
        $nodeKind = $root->getNodeKind();
        $this->assertInstanceOf(Element::class, $nodeKind);
        $this->assertEquals(ElementKind::Body, $nodeKind->getKind());

        $text = $root->getFirstChild();
        $this->assertNotNull($text);
        $this->assertEquals(LayoutObjectKind::Text, $text->getKind());

        // PHP版では、Textノードの場合は文字列を直接返す
        $textNodeKind = $text->getNodeKind();
        $this->assertEquals("text", $textNodeKind);
    }

    public function testDisplayNone(): void
    {
        $html = "<html><head><style>body{display:none;}</style></head><body>text</body></html>";
        $layoutView = self::createLayoutView($html);

        $this->assertNull($layoutView->getRoot());
    }

    public function testHiddenClass(): void
    {
        $html = <<<HTML
<html>
<head>
<style>
  .hidden {
    display: none;
  }
</style>
</head>
<body>
  <a class="hidden">link1</a>
  <p></p>
  <p class="hidden"><a>link2</a></p>
</body>
</html>
HTML;
        $layoutView = self::createLayoutView($html);

        $root = $layoutView->getRoot();
        $this->assertNotNull($root);
        $this->assertEquals(LayoutObjectKind::Block, $root->getKind());

        // PHP版では、getNodeKind()はElementオブジェクトを直接返す
        $rootNodeKind = $root->getNodeKind();
        $this->assertInstanceOf(Element::class, $rootNodeKind);
        $this->assertEquals(ElementKind::Body, $rootNodeKind->getKind());

        $p = $root->getFirstChild();
        $this->assertNotNull($p);
        $this->assertEquals(LayoutObjectKind::Block, $p->getKind());

        $pNodeKind = $p->getNodeKind();
        $this->assertInstanceOf(Element::class, $pNodeKind);
        $this->assertEquals(ElementKind::P, $pNodeKind->getKind());

        $this->assertNull($p->getFirstChild());
        $this->assertNull($p->getNextSibling());
    }
}
