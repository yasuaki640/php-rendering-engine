<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\Tests;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\HtmlParser;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\InsertionMode;

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

    /**
     * head要素とbody要素の兄弟関係を詳細にテスト（Rustのtest_bodyとtest_textと同等）
     */
    public function testBodyStructure(): void
    {
        // 基本的なDOM構造のテスト
        $html = '<html><head></head><body></body></html>';
        $parser = new HtmlParser($html);

        $window = $parser->constructTree();
        $document = $window->getDocument();

        // ドキュメントノードの確認
        $this->assertNotNull($document);
        $this->assertEquals(NodeKind::Document, $document->getKind());

        // HTML要素がドキュメントの最初の子であることを確認
        $htmlElement = $document->getFirstChild();
        $this->assertNotNull($htmlElement);
        $this->assertEquals(NodeKind::Element, $htmlElement->getKind());
        $this->assertEquals('html', $htmlElement->getElementKind()?->value);

        // HEAD要素がHTML要素の最初の子であることを確認
        $headElement = $htmlElement->getFirstChild();
        $this->assertNotNull($headElement);
        $this->assertEquals(NodeKind::Element, $headElement->getKind());
        $this->assertEquals('head', $headElement->getElementKind()?->value);

        // BODY要素がHEAD要素の次の兄弟であることを確認
        $bodyElement = $headElement->getNextSibling();
        $this->assertNotNull($bodyElement);
        $this->assertEquals(NodeKind::Element, $bodyElement->getKind());
        $this->assertEquals('body', $bodyElement->getElementKind()?->value);

        // BODY要素の次の兄弟が存在しないことを確認
        $this->assertNull($bodyElement->getNextSibling());

        // HEAD要素の前の兄弟が存在しないことを確認
        $this->assertNull($headElement->getPreviousSibling());

        // テキストノードのテスト（Rustのtest_textと同等）
        $htmlWithText = '<html><head></head><body>text</body></html>';
        $parserWithText = new HtmlParser($htmlWithText);

        $windowWithText = $parserWithText->constructTree();
        $documentWithText = $windowWithText->getDocument();

        // HTML要素からBODY要素を取得
        $htmlElementWithText = $documentWithText->getFirstChild();
        $this->assertNotNull($htmlElementWithText);

        $headElementWithText = $htmlElementWithText->getFirstChild();
        $this->assertNotNull($headElementWithText);

        $bodyElementWithText = $headElementWithText->getNextSibling();
        $this->assertNotNull($bodyElementWithText);
        $this->assertEquals('body', $bodyElementWithText->getElementKind()?->value);

        // BODY要素の子ノードがテキストノードであることを確認
        $textNode = $bodyElementWithText->getFirstChild();
        $this->assertNotNull($textNode);
        $this->assertEquals(NodeKind::Text, $textNode->getKind());
        $this->assertEquals('text', $textNode->getTextContent());

        // テキストノードに兄弟ノードが存在しないことを確認
        $this->assertNull($textNode->getNextSibling());
        $this->assertNull($textNode->getPreviousSibling());
    }

    /**
     * 複数のネストした要素の処理テスト（Rustのtest_multiple_nodesと同等）
     * <html><head></head><body><p><a foo=bar>text</a></p></body></html>のような
     * 複雑なDOM構造と属性処理を検証
     */
    public function testMultipleNodes(): void
    {
        $html = '<html><head></head><body><p><a foo=bar>text</a></p></body></html>';
        $parser = new HtmlParser($html);

        $window = $parser->constructTree();
        $document = $window->getDocument();

        // ドキュメントノードの確認
        $this->assertNotNull($document);
        $this->assertEquals(NodeKind::Document, $document->getKind());

        // HTML要素の確認
        $htmlElement = $document->getFirstChild();
        $this->assertNotNull($htmlElement);
        $this->assertEquals(NodeKind::Element, $htmlElement->getKind());
        $this->assertEquals('html', $htmlElement->getElementKind()?->value);

        // HEAD要素の確認（HTMLの最初の子）
        $headElement = $htmlElement->getFirstChild();
        $this->assertNotNull($headElement);
        $this->assertEquals(NodeKind::Element, $headElement->getKind());
        $this->assertEquals('head', $headElement->getElementKind()?->value);

        // BODY要素の確認（HEADの次の兄弟）
        $bodyElement = $headElement->getNextSibling();
        $this->assertNotNull($bodyElement);
        $this->assertEquals(NodeKind::Element, $bodyElement->getKind());
        $this->assertEquals('body', $bodyElement->getElementKind()?->value);

        // P要素の確認（BODYの最初の子）
        $pElement = $bodyElement->getFirstChild();
        $this->assertNotNull($pElement);
        $this->assertEquals(NodeKind::Element, $pElement->getKind());
        $this->assertEquals('p', $pElement->getElementKind()?->value);

        // A要素の確認（Pの最初の子、foo=bar属性付き）
        $aElement = $pElement->getFirstChild();
        $this->assertNotNull($aElement);
        $this->assertEquals(NodeKind::Element, $aElement->getKind());
        $this->assertEquals('a', $aElement->getElementKind()?->value);

        // A要素の属性確認（foo=bar）
        $aElementDom = $aElement->getElement();
        $this->assertNotNull($aElementDom);
        $this->assertTrue($aElementDom->hasAttribute('foo'));

        $fooAttribute = $aElementDom->getAttribute('foo');
        $this->assertNotNull($fooAttribute);
        $this->assertEquals('foo', $fooAttribute->name);
        $this->assertEquals('bar', $fooAttribute->value);

        // テキストノードの確認（Aの最初の子）
        $textNode = $aElement->getFirstChild();
        $this->assertNotNull($textNode);
        $this->assertEquals(NodeKind::Text, $textNode->getKind());
        $this->assertEquals('text', $textNode->getTextContent());

        // 兄弟関係の確認
        $this->assertNull($textNode->getNextSibling());  // テキストノードに次の兄弟なし
        $this->assertNull($aElement->getNextSibling());  // A要素に次の兄弟なし
        $this->assertNull($pElement->getNextSibling());  // P要素に次の兄弟なし
        $this->assertNull($bodyElement->getNextSibling());  // BODY要素に次の兄弟なし
        $this->assertNull($headElement->getPreviousSibling());  // HEAD要素に前の兄弟なし

        // 親子関係の確認
        $this->assertEquals($aElement, $textNode->getParent());
        $this->assertEquals($pElement, $aElement->getParent());
        $this->assertEquals($bodyElement, $pElement->getParent());
        $this->assertEquals($htmlElement, $bodyElement->getParent());
        $this->assertEquals($htmlElement, $headElement->getParent());
        $this->assertEquals($document, $htmlElement->getParent());
    }
}
