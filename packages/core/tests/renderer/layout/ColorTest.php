<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Renderer\Layout;

use MyApp\Core\Exception\UnexpectedInputException;
use MyApp\Core\Renderer\Layout\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testFromName(): void
    {
        $color = Color::fromName('black');
        $this->assertEquals('#000000', $color->getCode());
        $this->assertEquals('black', $color->getName());
    }

    public function testFromNameUnsupported(): void
    {
        $this->expectException(UnexpectedInputException::class);
        Color::fromName('unsupported');
    }

    public function testFromCode(): void
    {
        $color = Color::fromCode('#000000');
        $this->assertEquals('#000000', $color->getCode());
        $this->assertEquals('black', $color->getName());
    }

    public function testFromCodeInvalid(): void
    {
        $this->expectException(UnexpectedInputException::class);
        Color::fromCode('invalid');
    }

    public function testWhite(): void
    {
        $color = Color::white();
        $this->assertEquals('#ffffff', $color->getCode());
        $this->assertEquals('white', $color->getName());
    }

    public function testBlack(): void
    {
        $color = Color::black();
        $this->assertEquals('#000000', $color->getCode());
        $this->assertEquals('black', $color->getName());
    }

    public function testGetCodeAsInt(): void
    {
        $color = Color::fromCode('#ff0000');
        $this->assertEquals(0xff0000, $color->getCodeAsInt());
    }

    public function testEquals(): void
    {
        $color1 = Color::black();
        $color2 = Color::fromCode('#000000');
        $color3 = Color::white();

        $this->assertTrue($color1->equals($color2));
        $this->assertFalse($color1->equals($color3));
    }

    public function testToString(): void
    {
        $color = Color::fromName('red');
        $this->assertEquals('#ff0000', (string) $color);
    }
}
