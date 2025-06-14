#!/usr/bin/env php
<?php

require_once __DIR__ . '/packages/core/vendor/autoload.php';

use Yasuaki640\PhpRenderingEngine\Core\Examples\RenderingDemo;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Page;

echo "Debug CSS Application\n";
echo "====================\n\n";

// サンプルページ2をテスト
$html = RenderingDemo::getSamplePage2();
$response = RenderingDemo::createHttpResponse($html);

$page = new Page();
$page->receiveResponse($response);

$displayItems = $page->getDisplayItems();

echo "Display Items for Sample Page 2:\n";
foreach ($displayItems as $i => $item) {
    echo "Item $i: " . get_class($item) . "\n";

    if ($item instanceof \Yasuaki640\PhpRenderingEngine\Core\RectDisplayItem) {
        $style = $item->getStyle();
        echo "  Background Color: " . $style->getBackgroundColor()->getCode() . "\n";
        echo "  Text Color: " . $style->getColor()->getCode() . "\n";
        echo "  Display: " . $style->getDisplay()->value . "\n";

        $pos = $item->getLayoutPoint();
        echo "  Position: (" . $pos->getX() . ", " . $pos->getY() . ")\n";

        $size = $item->getLayoutSize();
        echo "  Size: " . $size->getWidth() . "x" . $size->getHeight() . "\n";
    }

    if ($item instanceof \Yasuaki640\PhpRenderingEngine\Core\TextDisplayItem) {
        $style = $item->getStyle();
        echo "  Text: \"" . $item->getText() . "\"\n";
        echo "  Text Color: " . $style->getColor()->getCode() . "\n";
        echo "  Font Size: " . $style->getFontSize()->value . "\n";

        $pos = $item->getLayoutPoint();
        echo "  Position: (" . $pos->getX() . ", " . $pos->getY() . ")\n";
    }

    echo "\n";
}
