<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Examples;

use Yasuaki640\PhpRenderingEngine\Core\HttpResponse;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Image\ImageRenderer;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Page;

/**
 * サンプルHTMLページの描画デモ
 */
class RenderingDemo
{
    /**
     * サンプルページ1を生成（ch6のtest1.htmlに対応）
     */
    public static function getSamplePage1(): string
    {
        return <<<'HTML'
<html>
<head>
  <style type="text/css">
    h1 {
      color: orange;
    }
    .red {
      background-color: red;
    }
  </style>    
</head>
<body>
  <h1>Test Page 1</h1>
  <p class="red">This is a test page with red background color.</p>
  <p><a href="http://host.test:8000/test2.html">Go to Page 2</a></p>
</body>
</html>
HTML;
    }

    /**
     * サンプルページ2を生成（ch6のtest2.htmlに対応）
     */
    public static function getSamplePage2(): string
    {
        return <<<'HTML'
<!doctype html>
<html>
<head>
  <title>title tag is unsupported</title>
  <style type="text/css">
    #blue {
      background-color: #0000ff;
    }
    .none {
      display: none;
    }
  </style>    
</head>
<body>
  <h1 id="blue">Test Page 2</h1>
  <a class="none">First inline element.</a>
  <a class="none">Second inline element.</a>
  <p><a href="http://host.test:8000/test1.html">Go to Page 1</a></p>
</body>
</html>
HTML;
    }

    /**
     * シンプルなテストページを生成
     */
    public static function getSimpleTestPage(): string
    {
        return <<<'HTML'
<html>
<body>
  <h1>Test Page</h1>
  <p>Hello World!</p>
</body>
</html>
HTML;
    }

    /**
     * HTMLコンテンツからHttpResponseを作成
     */
    public static function createHttpResponse(string $html): HttpResponse
    {
        $rawResponse = "HTTP/1.1 200 OK\r\n" .
            "Content-Type: text/html; charset=utf-8\r\n" .
            "Content-Length: " . strlen($html) . "\r\n" .
            "\r\n" .
            $html;

        return new HttpResponse($rawResponse);
    }

    /**
     * サンプルページをレンダリングして画像として保存
     */
    public static function renderSamplePageToImage(string $filename = 'sample-output.png'): void
    {
        // サンプルHTMLを取得
        $html = self::getSimpleTestPage();

        // HttpResponseを作成
        $response = self::createHttpResponse($html);

        // ページオブジェクトを作成
        $page = new Page();

        try {
            // HTMLをパースしてレンダリング
            $page->receiveResponse($response);

            // DisplayItemsを取得
            $displayItems = $page->getDisplayItems();

            echo "Generated " . count($displayItems) . " display items\n";

            // 画像レンダラーでDisplayItemsを描画
            $renderer = new ImageRenderer(800, 600);
            $renderer->render($displayItems);

            // 画像をファイルに保存
            if ($renderer->saveToFile($filename)) {
                echo "Successfully saved rendered page to: $filename\n";
            } else {
                echo "Failed to save image to: $filename\n";
            }
        } catch (\Exception $e) {
            echo "Error rendering page: " . $e->getMessage() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }

    /**
     * 複数のサンプルページをレンダリング
     */
    public static function renderAllSamplePages(): void
    {
        $samples = [
            'simple-test.png' => self::getSimpleTestPage(),
            'sample-page-1.png' => self::getSamplePage1(),
            'sample-page-2.png' => self::getSamplePage2(),
        ];

        foreach ($samples as $filename => $html) {
            echo "Rendering $filename...\n";

            $response = self::createHttpResponse($html);
            $page = new Page();

            try {
                $page->receiveResponse($response);
                $displayItems = $page->getDisplayItems();

                echo "  Generated " . count($displayItems) . " display items\n";

                $renderer = new ImageRenderer(800, 600);
                $renderer->render($displayItems);

                if ($renderer->saveToFile($filename)) {
                    echo "  Successfully saved: $filename\n";
                } else {
                    echo "  Failed to save: $filename\n";
                }
            } catch (\Exception $e) {
                echo "  Error: " . $e->getMessage() . "\n";
            }
        }
    }
}
