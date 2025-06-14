<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Examples;

use Yasuaki640\PhpRenderingEngine\Core\Browser;
use Yasuaki640\PhpRenderingEngine\Core\HttpResponse;
use Yasuaki640\PhpRenderingEngine\Net\HttpClient;

/**
 * BrowserとPageの使用例
 */
class BrowserExample
{
    public function run(): void
    {
        // ブラウザーを作成
        $browser = new Browser();

        // 現在のページを取得
        $currentPage = $browser->getCurrentPage();

        // シンプルなHTMLでテスト
        $this->testWithSimpleHtml($currentPage);

        // 新しいページを追加してテスト
        $this->testWithNewPage($browser);

        // HTTPクライアントを使用したテスト（オプション）
        $this->testWithHttpClient($browser);
    }

    private function testWithSimpleHtml($page): void
    {
        $htmlContent = '<html><head><title>Test Page</title></head><body><h1>Hello, World!</h1><p>This is a test page.</p></body></html>';
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $page->receiveResponse($response);

        if ($page->isLoaded()) {
            $frame = $page->getFrame();
            $document = $frame->getDocument();

            // HTML要素を探す
            $htmlElement = $document->getFirstChild();
            if ($htmlElement && $htmlElement->getElementKind()?->value === 'html') {
                // HEAD要素を探す
                $child = $htmlElement->getFirstChild();
                while ($child) {
                    if ($child->getElementKind()?->value === 'head') {
                        break;
                    }
                    $child = $child->getNextSibling();
                }

                // BODY要素を探す
                $child = $htmlElement->getFirstChild();
                while ($child) {
                    if ($child->getElementKind()?->value === 'body') {
                        break;
                    }
                    $child = $child->getNextSibling();
                }
            }
        }
    }

    private function testWithNewPage(Browser $browser): void
    {
        $newPage = $browser->addPage();

        // 新しいページに移動
        $browser->setActivePageIndex(1);
        $currentPage = $browser->getCurrentPage();

        // 新しいページでHTMLをロード
        $htmlContent = '<body><div>New Page Content</div></body>';
        $rawResponse = "HTTP/1.1 200 OK\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $currentPage->receiveResponse($response);
    }

    private function testWithHttpClient(Browser $browser): void
    {
        try {
            $client = new HttpClient();
            // 実際のHTTPリクエストは環境によって失敗する可能性があるため、
            // ここではサンプルレスポンスを使用
            $sampleHtml = '<html><head><title>HTTP Example</title></head><body><h1>HTTP Response</h1></body></html>';
            $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\nContent-Length: " . strlen($sampleHtml) . "\n\n" . $sampleHtml;

            $response = new HttpResponse($rawResponse);
            $browser->getCurrentPage()->receiveResponse($response);
        } catch (\Exception $e) {
            // HTTP test failed, ignore
        }
    }
}
