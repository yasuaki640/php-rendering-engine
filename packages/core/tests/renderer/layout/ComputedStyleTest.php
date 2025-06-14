<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\Color;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\ComputedStyle;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\DisplayType;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\FontSize;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\TextDecoration;

class ComputedStyleTest extends TestCase
{
    public function testConstruct(): void
    {
        $style = new ComputedStyle();

        // 初期状態では例外が発生する
        $this->expectException(\RuntimeException::class);
        $style->getBackgroundColor();
    }

    public function testDefaulting(): void
    {
        $style = new ComputedStyle();
        $style->defaulting(null, null);

        // デフォルト値が設定される
        $this->assertTrue($style->getBackgroundColor()->equals(Color::white()));
        $this->assertTrue($style->getColor()->equals(Color::black()));
        $this->assertEquals(DisplayType::Block, $style->getDisplay());
        $this->assertEquals(FontSize::Medium, $style->getFontSize());
        $this->assertEquals(TextDecoration::None, $style->getTextDecoration());
        $this->assertEquals(0.0, $style->getHeight());
        $this->assertEquals(0.0, $style->getWidth());
    }

    public function testDefaultingWithParent(): void
    {
        $parentStyle = new ComputedStyle();
        $parentStyle->defaulting(null, null);
        $parentStyle->setBackgroundColor(Color::fromName('red'));
        $parentStyle->setColor(Color::fromName('blue'));

        $childStyle = new ComputedStyle();
        $childStyle->defaulting(null, $parentStyle);

        // 親から継承される（初期値と異なる場合のみ）
        $this->assertTrue($childStyle->getBackgroundColor()->equals(Color::fromName('red')));
        $this->assertTrue($childStyle->getColor()->equals(Color::fromName('blue')));
    }

    public function testSettersAndGetters(): void
    {
        $style = new ComputedStyle();

        $red = Color::fromName('red');
        $style->setBackgroundColor($red);
        $this->assertTrue($style->getBackgroundColor()->equals($red));

        $blue = Color::fromName('blue');
        $style->setColor($blue);
        $this->assertTrue($style->getColor()->equals($blue));

        $style->setDisplay(DisplayType::Inline);
        $this->assertEquals(DisplayType::Inline, $style->getDisplay());

        $style->setHeight(100.5);
        $this->assertEquals(100.5, $style->getHeight());

        $style->setWidth(200.7);
        $this->assertEquals(200.7, $style->getWidth());
    }

    public function testGettersThrowExceptionWhenNotSet(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: background_color');
        $style->getBackgroundColor();
    }

    public function testColorGetterThrowsException(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: color');
        $style->getColor();
    }

    public function testDisplayGetterThrowsException(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: display');
        $style->getDisplay();
    }

    public function testFontSizeGetterThrowsException(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: font_size');
        $style->getFontSize();
    }

    public function testTextDecorationGetterThrowsException(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: text_decoration');
        $style->getTextDecoration();
    }

    public function testHeightGetterThrowsException(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: height');
        $style->getHeight();
    }

    public function testWidthGetterThrowsException(): void
    {
        $style = new ComputedStyle();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('failed to access CSS property: width');
        $style->getWidth();
    }
}
