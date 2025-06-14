#!/usr/bin/env php
<?php

require_once __DIR__ . '/packages/core/vendor/autoload.php';

use Yasuaki640\PhpRenderingEngine\Core\Examples\RenderingDemo;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Page;

echo "PHP Rendering Engine - Debug Sample Page 2\n";
echo "==========================================\n\n";

try {
    // サンプルページ2のHTMLを取得
    $html = RenderingDemo::getSamplePage2();
    echo "HTML content:\n";
    echo $html . "\n\n";

    // HttpResponseを作成
    $response = RenderingDemo::createHttpResponse($html);
    
    // ページオブジェクトを作成
    $page = new Page();
    
    // HTMLをパースしてレンダリング
    $page->receiveResponse($response);
    
    // DisplayItemsを取得
    $displayItems = $page->getDisplayItems();
    
    echo "Generated " . count($displayItems) . " display items:\n";
    foreach ($displayItems as $i => $item) {
        echo "  Item $i: " . get_class($item) . "\n";
        if ($item instanceof \Yasuaki640\PhpRenderingEngine\Core\TextDisplayItem) {
            echo "    Text: " . $item->getText() . "\n";
        }
        echo "    Position: (" . $item->getLayoutPoint()->getX() . ", " . $item->getLayoutPoint()->getY() . ")\n";
        echo "    Background Color: " . $item->getStyle()->getBackgroundColor()->getCode() . "\n";
        echo "    Text Color: " . $item->getStyle()->getColor()->getCode() . "\n";
        echo "    Display: " . $item->getStyle()->getDisplay()->name . "\n";
        echo "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
