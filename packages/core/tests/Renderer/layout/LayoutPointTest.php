<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutPoint;

class LayoutPointTest extends TestCase
{
    public function testConstruct(): void
    {
        $point = new LayoutPoint(10, 20);
        $this->assertEquals(10, $point->getX());
        $this->assertEquals(20, $point->getY());
    }

    public function testSetX(): void
    {
        $point = new LayoutPoint(0, 0);
        $point->setX(15);
        $this->assertEquals(15, $point->getX());
    }

    public function testSetY(): void
    {
        $point = new LayoutPoint(0, 0);
        $point->setY(25);
        $this->assertEquals(25, $point->getY());
    }

    public function testEquals(): void
    {
        $point1 = new LayoutPoint(10, 20);
        $point2 = new LayoutPoint(10, 20);
        $point3 = new LayoutPoint(15, 25);

        $this->assertTrue($point1->equals($point2));
        $this->assertFalse($point1->equals($point3));
    }

    public function testToString(): void
    {
        $point = new LayoutPoint(10, 20);
        $this->assertEquals('LayoutPoint(10, 20)', (string) $point);
    }
}
