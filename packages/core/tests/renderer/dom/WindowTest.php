<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests\Dom;

use MyApp\Core\Renderer\Dom\Node;
use MyApp\Core\Renderer\Dom\NodeKind;
use MyApp\Core\Renderer\Dom\Window;
use PHPUnit\Framework\TestCase;

class WindowTest extends TestCase
{
    public function testCreateWindow(): void
    {
        $window = new Window();
        $document = $window->getDocument();

        $this->assertInstanceOf(Node::class, $document);
        $this->assertEquals(NodeKind::Document, $document->getKind());
        $this->assertSame($window, $document->getWindow());
    }

    public function testDocumentHasWindowReference(): void
    {
        $window = new Window();
        $document = $window->getDocument();

        $this->assertSame($window, $document->getWindow());
    }
}
