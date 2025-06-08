<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Renderer\Layout;

use MyApp\Core\Renderer\Layout\LayoutSize;
use PHPUnit\Framework\TestCase;

class LayoutSizeTest extends TestCase
{
    public function testConstruct(): void
    {
        $size = new LayoutSize(100, 200);
        $this->assertEquals(100, $size->getWidth());
        $this->assertEquals(200, $size->getHeight());
    }

    public function testSetWidth(): void
    {
        $size = new LayoutSize(0, 0);
        $size->setWidth(150);
        $this->assertEquals(150, $size->getWidth());
    }

    public function testSetHeight(): void
    {
        $size = new LayoutSize(0, 0);
        $size->setHeight(250);
        $this->assertEquals(250, $size->getHeight());
    }

    public function testEquals(): void
    {
        $size1 = new LayoutSize(100, 200);
        $size2 = new LayoutSize(100, 200);
        $size3 = new LayoutSize(150, 250);

        $this->assertTrue($size1->equals($size2));
        $this->assertFalse($size1->equals($size3));
    }

    public function testToString(): void
    {
        $size = new LayoutSize(100, 200);
        $this->assertEquals('LayoutSize(100, 200)', (string) $size);
    }
}
