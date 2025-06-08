<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Renderer\Layout;

use MyApp\Core\Renderer\Css\CssParser;
use MyApp\Core\Renderer\Css\CssTokenizer;
use MyApp\Core\Renderer\Dom\Api;
use MyApp\Core\Renderer\Html\HtmlParser;
use MyApp\Core\Renderer\Layout\LayoutObject;
use MyApp\Core\Renderer\Layout\LayoutView;
use PHPUnit\Framework\TestCase;

class LayoutViewTest extends TestCase
{
    /**
     * Helper function to create LayoutView from HTML string.
     * This is equivalent to Rust's create_layout_view function.
     */
    private function createLayoutView(string $html): LayoutView
    {
        // Parse HTML to DOM
        $htmlParser = new HtmlParser($html);
        $window = $htmlParser->constructTree();
        $document = $window->getDocument();

        // Extract style content from DOM
        $styleContent = Api::getStyleContent($document);

        // Tokenize and parse CSS
        $cssTokenizer = new CssTokenizer($styleContent);
        $cssParser = new CssParser($cssTokenizer);
        $cssRules = $cssParser->parseStylesheet();

        // Create LayoutView - pass document to match Rust behavior which uses body as root
        return new LayoutView($document, $cssRules);
    }

    /**
     * Test that empty HTML string returns null root.
     * Corresponds to Rust test_empty.
     */
    public function testEmpty(): void
    {
        $layoutView = $this->createLayoutView('');
        $this->assertNull($layoutView->getRoot());
    }

    /**
     * Test that basic HTML with body element creates proper LayoutObject.
     * Corresponds to Rust test_body.
     */
    public function testBody(): void
    {
        $html = '<html><head></head><body></body></html>';
        $layoutView = $this->createLayoutView($html);

        $root = $layoutView->getRoot();
        $this->assertInstanceOf(LayoutObject::class, $root);

        // Root should be body element (matching Rust behavior)
        $nodeKind = $root->getNodeKind();
        $this->assertInstanceOf(\MyApp\Core\Renderer\Dom\Element::class, $nodeKind);
        $this->assertEquals('body', $nodeKind->getKind()->value);
    }

    /**
     * Test that HTML with text content creates text LayoutObject as child.
     * Corresponds to Rust test_text.
     */
    public function testText(): void
    {
        $html = '<html><head></head><body>text</body></html>';
        $layoutView = $this->createLayoutView($html);

        $root = $layoutView->getRoot();
        $this->assertInstanceOf(LayoutObject::class, $root);

        // Root should be body element (matching Rust behavior)
        $nodeKind = $root->getNodeKind();
        $this->assertInstanceOf(\MyApp\Core\Renderer\Dom\Element::class, $nodeKind);
        $this->assertEquals('body', $nodeKind->getKind()->value);

        // Body should have text child
        $textLayout = $root->getFirstChild();
        $this->assertNotNull($textLayout);

        $textNodeKind = $textLayout->getNodeKind();
        $this->assertIsString($textNodeKind); // Text nodes return string
        $this->assertEquals('text', $textNodeKind);
    }

    /**
     * Test that CSS display:none prevents LayoutObject creation.
     * Corresponds to Rust test_display_none.
     */
    public function testDisplayNone(): void
    {
        $html = '<html><head><style>body{display:none;}</style></head><body>text</body></html>';
        $layoutView = $this->createLayoutView($html);

        // Should have no root because body has display: none (matching Rust behavior)
        $root = $layoutView->getRoot();
        $this->assertNull($root);
    }

    /**
     * Test complex HTML with .hidden { display: none; } class.
     * Corresponds to Rust test_hidden_class.
     */
    public function testHiddenClass(): void
    {
        $html = '<html>
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
</html>';

        $layoutView = $this->createLayoutView($html);

        $root = $layoutView->getRoot();
        $this->assertInstanceOf(LayoutObject::class, $root);

        // Root should be body element (matching Rust behavior)
        $nodeKind = $root->getNodeKind();
        $this->assertInstanceOf(\MyApp\Core\Renderer\Dom\Element::class, $nodeKind);
        $this->assertEquals('body', $nodeKind->getKind()->value);

        // Body should have only the visible p element (hidden elements should not appear)
        $pElement = $root->getFirstChild();
        $this->assertNotNull($pElement);

        $pNodeKind = $pElement->getNodeKind();
        $this->assertInstanceOf(\MyApp\Core\Renderer\Dom\Element::class, $pNodeKind);
        $this->assertEquals('p', $pNodeKind->getKind()->value);

        // P element should have no children (empty p)
        $this->assertNull($pElement->getFirstChild());

        // No next sibling (hidden elements should not be rendered)
        $this->assertNull($pElement->getNextSibling());
    }
}
