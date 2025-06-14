<?php

namespace Yasuaki640\PhpRenderingEngine;

use Yasuaki640\PhpRenderingEngine\Core\Browser;
use Yasuaki640\PhpRenderingEngine\Core\DomUtils;
use Yasuaki640\PhpRenderingEngine\Core\Examples\BrowserExample;
use Yasuaki640\PhpRenderingEngine\Core\Examples\RenderingDemo;
use Yasuaki640\PhpRenderingEngine\Core\HttpResponse;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\ImageRenderer;
use Yasuaki640\PhpRenderingEngine\Net\HttpClient;
use Yasuaki640\PhpRenderingEngine\UriParser\Url;

class CLI
{
    public function run(): void
    {
        $args = $_SERVER['argv'] ?? [];

        if (count($args) > 1 && $args[1] === 'test-http') {
            $this->testHttpClient();
        } elseif (count($args) > 1 && $args[1] === 'test-example') {
            $this->testExampleCom();
        } elseif (count($args) > 1 && $args[1] === 'test-browser') {
            $this->testBrowser();
        } elseif (count($args) > 1 && $args[1] === 'test-browser-example') {
            $this->runBrowserExample();
        } elseif (count($args) > 1 && $args[1] === 'render-samples') {
            $this->renderSamplePages();
        } elseif (count($args) > 1 && $args[1] === 'render-example') {
            $this->renderExample();
        } else {
            echo "Available commands:\n";
            echo "  test-http           - Test HTTP client with httpbin.org\n";
            echo "  test-example        - Test HTTP client with example.com\n";
            echo "  test-browser        - Test Browser and Page classes (simple, based on ch5/main.rs)\n";
            echo "  test-browser-example - Run detailed BrowserExample class\n";
            echo "  render-samples      - Render all sample HTML pages to images (ch6 app.rs equivalent)\n";
            echo "  render-example      - Render example.com using net and uri-parser packages\n";
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

    /**
     * 詳細なBrowserExampleクラスを実行
     */
    private function runBrowserExample(): void
    {
        try {
            $example = new BrowserExample();
            $example->run();
            echo "\n=== BrowserExample completed successfully ===\n";
        } catch (\Exception $e) {
            echo "Error running BrowserExample: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }

    /**
     * サンプルページを全て描画（ch6のapp.rs相当の処理）
     */
    private function renderSamplePages(): void
    {
        echo "=== Rendering Sample Pages (ch6 app.rs equivalent) ===\n\n";

        try {
            // RenderingDemoクラスのrenderAllSamplePages()を使用
            RenderingDemo::renderAllSamplePages();
            echo "\n=== All sample pages rendered successfully ===\n";
        } catch (\Exception $e) {
            echo "Error rendering sample pages: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }

    /**
     * example.comを描画（netとuri-parserパッケージを使用）
     */
    private function renderExample(): void
    {
        echo "=== Rendering example.com (using net and uri-parser packages) ===\n\n";

        try {
            // URLをパース
            $url = 'http://example.com';
            echo "Parsing URL: $url\n";

            $urlObj = new Url($url);
            $parsedUrl = $urlObj->parse();

            echo "Parsed URL details:\n";
            echo "  Host: " . $parsedUrl->host . "\n";
            echo "  Port: " . $parsedUrl->port . "\n";
            echo "  Path: " . ($parsedUrl->path ?: '/') . "\n";
            echo "  Search part: " . $parsedUrl->searchpart . "\n\n";

            // HTTPクライアントを使用してリクエスト
            echo "Sending HTTP request...\n";
            $client = new HttpClient();
            $response = $client->get(
                $parsedUrl->host,
                (int) $parsedUrl->port,
                $parsedUrl->path ?: '/'
            );

            echo "Response received!\n";
            $this->printResponse($response);

            // ブラウザーでページを処理
            echo "\n--- Processing with Browser ---\n";
            $browser = new Browser();
            $currentPage = $browser->getCurrentPage();

            $domString = $currentPage->receiveResponse($response);
            echo "Page loaded: " . ($currentPage->isLoaded() ? 'Yes' : 'No') . "\n";

            if ($currentPage->isLoaded()) {
                $frame = $currentPage->getFrame();
                $document = $frame->getDocument();

                echo "\n--- DOM Tree Structure ---\n";
                $domTreeString = DomUtils::convertDomToString($document);
                echo $domTreeString;
                echo "--- End of DOM Tree ---\n";

                // 画像レンダリング処理を追加
                echo "\n--- Rendering to Image ---\n";
                try {
                    $displayItems = $currentPage->getDisplayItems();
                    echo "Generated " . count($displayItems) . " display items\n";

                    // 画像レンダラーを作成（800x600ピクセル）
                    $renderer = new ImageRenderer(800, 600);
                    $renderer->render($displayItems);

                    // ファイル名を生成（タイムスタンプ付き）
                    $filename = 'example-com-' . date('Y-m-d-H-i-s') . '.png';
                    $saved = $renderer->saveToFile($filename);

                    if ($saved) {
                        echo "✓ Image saved successfully to: $filename\n";
                        echo "  Image dimensions: 800x600 pixels\n";
                        echo "  File location: " . realpath($filename) . "\n";
                    } else {
                        echo "✗ Failed to save image to: $filename\n";
                    }
                } catch (\Exception $imageError) {
                    echo "Error during image rendering: " . $imageError->getMessage() . "\n";
                }
            }

        } catch (\Exception $e) {
            echo "Error rendering example.com: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }
}
