<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Dom;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Window;

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
