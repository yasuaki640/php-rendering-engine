<?php

namespace Yasuaki640\PhpRenderingEngine;

use MyApp\Core\Browser;
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

        // ブラウザーを作成
        $browser = new Browser();
        echo "Created browser with {$browser->getPageCount()} page(s)\n";

        // 現在のページを取得
        $currentPage = $browser->getCurrentPage();
        echo "Current page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n\n";

        // シンプルなHTMLでテスト
        echo "--- Testing with simple HTML ---\n";

        $htmlContent = '<html><head><title>Test Page</title></head><body><h1>Hello, World!</h1><p>This is a test page.</p></body></html>';
        $rawResponse = "HTTP/1.1 200 OK\nContent-Type: text/html\n\n" . $htmlContent;

        $response = new HttpResponse($rawResponse);
        $currentPage->receiveResponse($response);

        echo "Page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n";

        if ($currentPage->isLoaded()) {
            $frame = $currentPage->getFrame();
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

        echo "\n--- Testing with new page ---\n";

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

        echo "Browser and Page test completed successfully!\n";
    }
}
