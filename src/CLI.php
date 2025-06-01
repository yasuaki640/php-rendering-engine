<?php

namespace Yasuaki640\PhpRenderingEngine;

use MyApp\Core\Browser;
use MyApp\Core\DomUtils;
use MyApp\Core\HttpResponse;
use MyApp\Net\HttpClient;

class CLI
{
    public function run(): void
    {
        $args = $_SERVER['argv'] ?? [];

        if (count($args) > 1 && $args[1] === 'test-http') {
            $this->testHttpClient();
        } elseif (count($args) > 1 && $args[1] === 'test-host') {
            $this->testHostClient();
        } elseif (count($args) > 1 && $args[1] === 'test-example') {
            $this->testExampleCom();
        } elseif (count($args) > 1 && $args[1] === 'test-browser') {
            $this->testBrowser();
        } else {
            echo "Available commands:\n";
            echo "  test-http     - Test HTTP client with httpbin.org\n";
            echo "  test-host     - Test HTTP client with host.test:8000\n";
            echo "  test-example  - Test HTTP client with example.com\n";
            echo "  test-browser  - Test Browser and Page classes\n";
            echo "\nUsage: php bin/hello <command>\n";
        }
    }

    private function testHttpClient(): void
    {
        $client = new HttpClient();

        try {
            // 実際にアクセス可能なホストでテスト
            $response = $client->get("httpbin.org", 80, "get");
            echo "response:\n";
            $this->printResponse($response);
        } catch (\Exception $e) {
            echo "error:\n";
            echo $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function testHostClient(): void
    {
        $client = new HttpClient();

        try {
            // Rustコードと同じパラメータでテスト: host.test:8000/test.html
            $response = $client->get("host.test", 8000, "test.html");
            echo "response:\n";
            $this->printResponse($response);
        } catch (\Exception $e) {
            echo "error:\n";
            echo $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function testExampleCom(): void
    {
        $client = new HttpClient();

        try {
            // example.comでテスト（存在する実際のWebサイト）
            $response = $client->get("example.com", 80, "");
            echo "response:\n";
            $this->printResponse($response);
        } catch (\Exception $e) {
            echo "error:\n";
            echo $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n";
        }
    }

    private function printResponse(HttpResponse $response): void
    {
        echo "Version: " . $response->version . "\n";
        echo "Status Code: " . $response->statusCode . "\n";
        echo "Reason: " . $response->reason . "\n";
        echo "Headers:\n";

        foreach ($response->headers as $header) {
            echo "  " . $header->name . ": " . $header->value . "\n";
        }

        echo "Body:\n";
        echo $response->body . "\n";

        echo "\nRaw Response:\n";
        echo "============\n";
        echo $response->rawResponse . "\n";
    }

    private function testBrowser(): void
    {
        echo "=== Browser and Page Test ===\n\n";

        // main.rsのTEST_HTTP_RESPONSEを参考にしたテストHTTPレスポンス
        $testHttpResponse = <<<'HTML'
HTTP/1.1 200 OK
Data: xx xx xx


<html>
<head></head>
<body>
  <h1 id="title">H1 title</h1>
  <h2 class="class">H2 title</h2>
  <p>Test text.</p>
  <p>
    <a href="example.com">Link1</a>
    <a href="example.com">Link2</a>
  </p>
</body>
</html>
HTML;

        // ブラウザーを作成
        $browser = new Browser();
        echo "Created browser with {$browser->getPageCount()} page(s)\n";

        // 現在のページを取得
        $currentPage = $browser->getCurrentPage();
        echo "Current page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n\n";

        // テストHTTPレスポンスでテスト
        echo "--- Testing with test HTTP response (from main.rs) ---\n";

        try {
            $response = new HttpResponse($testHttpResponse);
            $domString = $currentPage->receiveResponse($response);

            echo "Page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n";

            if ($currentPage->isLoaded()) {
                $frame = $currentPage->getFrame();
                $document = $frame->getDocument();

                echo "Document created successfully\n\n";

                // DOMツリーを可視化（utils.rsのconvert_dom_to_string相当）
                echo "--- DOM Tree Structure ---\n";
                $domTreeString = DomUtils::convertDomToString($document);
                echo $domTreeString;
                echo "--- End of DOM Tree ---\n\n";

                // 基本的な要素の確認
                $this->verifyBasicElements($document);
            }
        } catch (\Exception $e) {
            echo "Error processing HTTP response: " . $e->getMessage() . "\n";
            echo "Stack trace:\n";
            echo $e->getTraceAsString() . "\n\n";
        }

        echo "\n--- Testing with simple HTML ---\n";

        // 新しいページでシンプルなHTMLをテスト
        $newPage = $browser->addPage();
        $browser->setActivePageIndex(1);
        $currentPage = $browser->getCurrentPage();

        $simpleHtmlContent = '<html><head><title>Simple Test</title></head><body><h1>Hello, World!</h1><p>This is a simple test page.</p></body></html>';
        $simpleRawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n" . $simpleHtmlContent;

        try {
            $response = new HttpResponse($simpleRawResponse);
            $currentPage->receiveResponse($response);

            echo "Simple page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n";

            if ($currentPage->isLoaded()) {
                $frame = $currentPage->getFrame();
                $document = $frame->getDocument();
                echo "\n--- Simple DOM Tree Structure ---\n";
                $domTreeString = DomUtils::convertDomToString($document);
                echo $domTreeString;
                echo "--- End of Simple DOM Tree ---\n";
            }
        } catch (\Exception $e) {
            echo "Error processing simple HTML: " . $e->getMessage() . "\n";
        }

        echo "\nBrowser and Page test completed successfully!\n";
    }

    /**
     * 基本的なHTML要素が正しく作成されているかを確認
     */
    private function verifyBasicElements($document): void
    {
        echo "--- Verifying basic elements ---\n";

        // HTML要素を探す
        $htmlElement = $document->getFirstChild();
        if ($htmlElement && $htmlElement->getElementKind()?->value === 'html') {
            echo "✓ HTML element found\n";

            // HEAD要素を探す
            $child = $htmlElement->getFirstChild();
            while ($child) {
                if ($child->getElementKind()?->value === 'head') {
                    echo "✓ HEAD element found\n";

                    break;
                }
                $child = $child->getNextSibling();
            }

            // BODY要素を探す
            $child = $htmlElement->getFirstChild();
            while ($child) {
                if ($child->getElementKind()?->value === 'body') {
                    echo "✓ BODY element found\n";

                    // BODY内のH1要素を探す
                    $bodyChild = $child->getFirstChild();
                    while ($bodyChild) {
                        if ($bodyChild->getElementKind()?->value === 'h1') {
                            echo "✓ H1 element found in BODY\n";

                            break;
                        }
                        $bodyChild = $bodyChild->getNextSibling();
                    }

                    break;
                }
                $child = $child->getNextSibling();
            }
        } else {
            echo "✗ HTML element not found\n";
        }

        echo "--- End of verification ---\n";
    }
}
