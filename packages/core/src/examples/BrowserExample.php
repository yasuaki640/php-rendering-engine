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
        echo "=== Browser and Page Example ===\n\n";

        // ブラウザーを作成
        $browser = new Browser();
        echo "Created browser with {$browser->getPageCount()} page(s)\n";

        // 現在のページを取得
        $currentPage = $browser->getCurrentPage();
        echo "Current page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n\n";

        // シンプルなHTMLでテスト
        $this->testWithSimpleHtml($currentPage);

        // 新しいページを追加してテスト
        $this->testWithNewPage($browser);

        // HTTPクライアントを使用したテスト（オプション）
        $this->testWithHttpClient($browser);
    }

    private function testWithSimpleHtml($page): void
    {
        echo "--- Testing with simple HTML ---\n";

        $htmlContent = '<html><head><title>Test Page</title></head><body><h1>Hello, World!</h1><p>This is a test page.</p></body></html>';
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $page->receiveResponse($response);

        echo "Page loaded: " . ($page->isLoaded() ? 'Yes' : 'No') . "\n";

        if ($page->isLoaded()) {
            $frame = $page->getFrame();
            $document = $frame->getDocument();

            echo "Document created successfully\n";

            // HTML要素を探す
            $htmlElement = $document->getFirstChild();
            if ($htmlElement && $htmlElement->getElementKind()?->value === 'html') {
                echo "HTML element found\n";

                // HEAD要素を探す
                $child = $htmlElement->getFirstChild();
                while ($child) {
                    if ($child->getElementKind()?->value === 'head') {
                        echo "HEAD element found\n";

                        break;
                    }
                    $child = $child->getNextSibling();
                }

                // BODY要素を探す
                $child = $htmlElement->getFirstChild();
                while ($child) {
                    if ($child->getElementKind()?->value === 'body') {
                        echo "BODY element found\n";

                        break;
                    }
                    $child = $child->getNextSibling();
                }
            }
        }

        echo "\n";
    }

    private function testWithNewPage(Browser $browser): void
    {
        echo "--- Testing with new page ---\n";

        $newPage = $browser->addPage();
        echo "Added new page. Total pages: {$browser->getPageCount()}\n";

        // 新しいページに移動
        $browser->setActivePageIndex(1);
        $currentPage = $browser->getCurrentPage();

        if ($currentPage === $newPage) {
            echo "Successfully switched to new page\n";
        }

        // 新しいページでHTMLをロード
        $htmlContent = '<body><div>New Page Content</div></body>';
        $rawResponse = "HTTP/1.1 200 OK\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $currentPage->receiveResponse($response);

        echo "New page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n\n";
    }

    private function testWithHttpClient(Browser $browser): void
    {
        echo "--- Testing with HTTP client (optional) ---\n";

        try {
            $client = new HttpClient();
            // 実際のHTTPリクエストは環境によって失敗する可能性があるため、
            // ここではサンプルレスポンスを使用
            $sampleHtml = '<html><head><title>HTTP Example</title></head><body><h1>HTTP Response</h1></body></html>';
            $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\nContent-Length: " . strlen($sampleHtml) . "\n\n" . $sampleHtml;

            $response = new HttpResponse($rawResponse);
            $browser->getCurrentPage()->receiveResponse($response);

            echo "HTTP response processed successfully\n";
        } catch (\Exception $e) {
            echo "HTTP test skipped: " . $e->getMessage() . "\n";
        }

        echo "\n";
    }
}
