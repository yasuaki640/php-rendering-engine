<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests;

use MyApp\Core\Browser;
use MyApp\Core\HttpResponse;
use MyApp\Core\Renderer\Page;
use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public function testCreatePage(): void
    {
        $page = new Page();

        $this->assertNull($page->getFrame());
        $this->assertFalse($page->isLoaded());
    }

    public function testSetAndGetBrowser(): void
    {
        $browser = new Browser();
        $page = new Page();

        $page->setBrowser($browser);

        $this->assertSame($browser, $page->getBrowser());
    }

    public function testReceiveResponse(): void
    {
        $page = new Page();
        $htmlContent = '<html><head><title>Test</title></head><body><p>Hello World</p></body></html>';
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $page->receiveResponse($response);

        $this->assertTrue($page->isLoaded());
        $this->assertNotNull($page->getFrame());

        // DOMツリーが正しく構築されているか確認
        $frame = $page->getFrame();
        $document = $frame->getDocument();
        $this->assertNotNull($document);

        // HTML要素が存在するか確認
        $htmlElement = $document->getFirstChild();
        $this->assertNotNull($htmlElement);
        $this->assertEquals('html', $htmlElement->getElementKind()?->value);
    }

    public function testReceiveResponseWithSimpleHtml(): void
    {
        $page = new Page();
        $htmlContent = '<body>Hello</body>';
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $page->receiveResponse($response);

        $this->assertTrue($page->isLoaded());
        $frame = $page->getFrame();
        $this->assertNotNull($frame);

        // ドキュメントが作成されているか確認
        $document = $frame->getDocument();
        $this->assertNotNull($document);
    }

    public function testClear(): void
    {
        $page = new Page();
        $htmlContent = '<html><body>Test</body></html>';
        $rawResponse = "HTTP/1.1 200 OK\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $page->receiveResponse($response);

        $this->assertTrue($page->isLoaded());

        $page->clear();

        $this->assertFalse($page->isLoaded());
        $this->assertNull($page->getFrame());
    }

    public function testWeakReferenceToBrowser(): void
    {
        $page = new Page();
        $browser = new Browser();

        $page->setBrowser($browser);
        $this->assertSame($browser, $page->getBrowser());

        // ブラウザーが破棄されると弱参照もnullになる
        unset($browser);

        // 弱参照は自動的にnullになる
        $this->assertNull($page->getBrowser());
    }
}
