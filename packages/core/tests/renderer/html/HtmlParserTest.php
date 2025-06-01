<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Html\Tests;

use MyApp\Core\Renderer\Dom\NodeKind;
use MyApp\Core\Renderer\Html\HtmlParser;
use MyApp\Core\Renderer\Html\InsertionMode;
use PHPUnit\Framework\TestCase;

/**
 * HtmlParserのテストクラス
 */
class HtmlParserTest extends TestCase
{
    /**
     * 基本的なHTML文書のパース
     */
    public function testConstructTreeBasicHtml(): void
    {
        $html = '<html><head><title>Test</title></head><body><p>Hello World</p></body></html>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $document = $window->getDocument();
        
        // ドキュメントノードが存在することを確認
        $this->assertNotNull($document);
        $this->assertEquals(NodeKind::Document, $document->getKind());
        
        // HTML要素が子として存在することを確認
        $htmlElement = $document->getFirstChild();
        $this->assertNotNull($htmlElement);
        $this->assertEquals(NodeKind::Element, $htmlElement->getKind());
        $this->assertEquals('html', $htmlElement->getElementKind()?->value);
    }

    /**
     * 暗黙的なhtml要素の挿入テスト
     */
    public function testConstructTreeImplicitHtml(): void
    {
        $html = '<body><p>Hello</p></body>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $document = $window->getDocument();
        
        // 暗黙的にHTML要素が作成されることを確認
        $htmlElement = $document->getFirstChild();
        $this->assertNotNull($htmlElement);
        $this->assertEquals('html', $htmlElement->getElementKind()?->value);
    }

    /**
     * テキストノードの処理テスト
     */
    public function testConstructTreeWithText(): void
    {
        $html = '<html><body>Hello World</body></html>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $document = $window->getDocument();
        
        // HTML → body → テキストノードの構造を確認
        $htmlElement = $document->getFirstChild();
        $this->assertNotNull($htmlElement);
        
        $bodyElement = null;
        $child = $htmlElement->getFirstChild();
        while ($child !== null) {
            if ($child->getElementKind()?->value === 'body') {
                $bodyElement = $child;
                break;
            }
            $child = $child->getNextSibling();
        }
        
        $this->assertNotNull($bodyElement);
        
        // bodyの子要素にテキストノードが存在することを確認
        $textNode = $bodyElement->getFirstChild();
        $this->assertNotNull($textNode);
        $this->assertEquals(NodeKind::Text, $textNode->getKind());
        $this->assertEquals('Hello World', $textNode->getTextContent());
    }

    /**
     * 空のHTML文書のテスト
     */
    public function testConstructTreeEmptyHtml(): void
    {
        $html = '';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $document = $window->getDocument();
        
        $this->assertNotNull($document);
        $this->assertEquals(NodeKind::Document, $document->getKind());
    }

    /**
     * 自己終了タグのテスト
     */
    public function testConstructTreeSelfClosingTags(): void
    {
        $html = '<html><body><br/><img src="test.jpg"/></body></html>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $this->assertNotNull($window);
        
        // パースが正常に完了することを確認（詳細なDOM構造の検証は省略）
        $this->assertEquals(InsertionMode::AfterAfterBody, $parser->getMode());
    }

    /**
     * スクリプトタグのテスト
     */
    public function testConstructTreeScriptTag(): void
    {
        $html = '<html><head><script>alert("test");</script></head><body></body></html>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $this->assertNotNull($window);
        
        // パースが正常に完了することを確認
        $this->assertEquals(InsertionMode::AfterAfterBody, $parser->getMode());
    }

    /**
     * ネストした要素のテスト
     */
    public function testConstructTreeNestedElements(): void
    {
        $html = '<html><body><div><p>Nested <span>content</span></p></div></body></html>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $this->assertNotNull($window);
        
        // パースが正常に完了することを確認
        $this->assertEquals(InsertionMode::AfterAfterBody, $parser->getMode());
    }

    /**
     * 属性付き要素のテスト
     */
    public function testConstructTreeElementsWithAttributes(): void
    {
        $html = '<html><body><a href="https://example.com" class="link">Link</a></body></html>';
        $parser = new HtmlParser($html);
        
        $window = $parser->constructTree();
        $this->assertNotNull($window);
        
        // パースが正常に完了することを確認
        $this->assertEquals(InsertionMode::AfterAfterBody, $parser->getMode());
    }

    /**
     * 現在のモード取得テスト
     */
    public function testGetMode(): void
    {
        $parser = new HtmlParser('<html><body></body></html>');
        
        // 初期モード
        $this->assertEquals(InsertionMode::Initial, $parser->getMode());
        
        // パース後のモード
        $parser->constructTree();
        $this->assertEquals(InsertionMode::AfterAfterBody, $parser->getMode());
    }

    /**
     * スタック取得テスト
     */
    public function testGetStackOfOpenElements(): void
    {
        $parser = new HtmlParser('<html><body><p></p></body></html>');
        
        // 初期状態では空
        $this->assertEmpty($parser->getStackOfOpenElements());
        
        // パース後は空（すべて正常に閉じられているため）
        $parser->constructTree();
        $this->assertEmpty($parser->getStackOfOpenElements());
    }
}
