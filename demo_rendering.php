#!/usr/bin/env php
<?php

require_once __DIR__ . '/packages/core/vendor/autoload.php';

use Yasuaki640\PhpRenderingEngine\Core\Examples\RenderingDemo;

echo "PHP Rendering Engine - Sample Page Demo\n";
echo "========================================\n\n";

try {
    // シンプルなテストページをレンダリング
    echo "Rendering simple test page...\n";
    RenderingDemo::renderSamplePageToImage('simple-test-output.png');

    echo "\nRendering all sample pages...\n";
    RenderingDemo::renderAllSamplePages();

    echo "\nDemo completed successfully!\n";
} catch (Exception $e) {
    echo "Error running demo: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
