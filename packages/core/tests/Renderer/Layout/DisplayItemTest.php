<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\ComputedStyle;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\DisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutPoint;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutSize;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\RectDisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\TextDisplayItem;

class DisplayItemTest extends TestCase
{
    public function testCreateRect(): void
    {
        $style = new ComputedStyle();
        $point = new LayoutPoint(10, 20);
        $size = new LayoutSize(100, 200);

        $item = DisplayItem::createRect($style, $point, $size);

        $this->assertInstanceOf(RectDisplayItem::class, $item);
        $this->assertSame($style, $item->getStyle());
        $this->assertSame($point, $item->getLayoutPoint());
        $this->assertSame($size, $item->getLayoutSize());
    }

    public function testCreateText(): void
    {
        $text = 'Hello World';
        $style = new ComputedStyle();
        $point = new LayoutPoint(10, 20);

        $item = DisplayItem::createText($text, $style, $point);

        $this->assertInstanceOf(TextDisplayItem::class, $item);
        $this->assertEquals($text, $item->getText());
        $this->assertSame($style, $item->getStyle());
        $this->assertSame($point, $item->getLayoutPoint());
    }
}

class RectDisplayItemTest extends TestCase
{
    public function testConstruct(): void
    {
        $style = new ComputedStyle();
        $point = new LayoutPoint(10, 20);
        $size = new LayoutSize(100, 200);

        $item = new RectDisplayItem($style, $point, $size);

        $this->assertSame($style, $item->getStyle());
        $this->assertSame($point, $item->getLayoutPoint());
        $this->assertSame($size, $item->getLayoutSize());
    }
}

class TextDisplayItemTest extends TestCase
{
    public function testConstruct(): void
    {
        $text = 'Hello World';
        $style = new ComputedStyle();
        $point = new LayoutPoint(10, 20);

        $item = new TextDisplayItem($text, $style, $point);

        $this->assertEquals($text, $item->getText());
        $this->assertSame($style, $item->getStyle());
        $this->assertSame($point, $item->getLayoutPoint());
    }
}
