#!/usr/bin/env php
<?php

require_once __DIR__ . '/packages/core/vendor/autoload.php';

use Yasuaki640\PhpRenderingEngine\Core\Examples\RenderingDemo;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\HtmlParser;

echo "Debug HTML Parser\n";
echo "================\n\n";

// サンプルページ2のHTMLを取得
$html = RenderingDemo::getSamplePage2();

echo "HTML content:\n";
echo $html . "\n\n";

echo "Parsing HTML...\n";

$parser = new HtmlParser($html);
$window = $parser->constructTree();

echo "DOM Tree constructed.\n";

function printDOMTree($node, $indent = 0)
{
    $prefix = str_repeat("  ", $indent);

    if ($node->getKind() === \Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind::Element) {
        $element = $node->getElement();
        if ($element) {
            echo $prefix . "Element: " . $element->getKind()->value . "\n";
            foreach ($element->getAttributes() as $attr) {
                echo $prefix . "  @" . $attr->name . "=" . $attr->value . "\n";
            }
        }
    } elseif ($node->getKind() === \Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind::Text) {
        $text = $node->getTextContent();
        if ($text && trim($text) !== '') {
            echo $prefix . "Text: \"" . trim($text) . "\"\n";
        }
    }

    // 子ノードを再帰的に処理
    $child = $node->getFirstChild();
    while ($child !== null) {
        printDOMTree($child, $indent + 1);
        $child = $child->getNextSibling();
    }
}

echo "\nDOM Tree:\n";
printDOMTree($window->getDocument());
