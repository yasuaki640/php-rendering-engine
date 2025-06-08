<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

abstract class DisplayItem
{
    public static function createRect(ComputedStyle $style, LayoutPoint $layoutPoint, LayoutSize $layoutSize): RectDisplayItem
    {
        return new RectDisplayItem($style, $layoutPoint, $layoutSize);
    }

    public static function createText(string $text, ComputedStyle $style, LayoutPoint $layoutPoint): TextDisplayItem
    {
        return new TextDisplayItem($text, $style, $layoutPoint);
    }
}

class RectDisplayItem extends DisplayItem
{
    private ComputedStyle $style;
    private LayoutPoint $layoutPoint;
    private LayoutSize $layoutSize;

    public function __construct(ComputedStyle $style, LayoutPoint $layoutPoint, LayoutSize $layoutSize)
    {
        $this->style = $style;
        $this->layoutPoint = $layoutPoint;
        $this->layoutSize = $layoutSize;
    }

    public function getStyle(): ComputedStyle
    {
        return $this->style;
    }

    public function getLayoutPoint(): LayoutPoint
    {
        return $this->layoutPoint;
    }

    public function getLayoutSize(): LayoutSize
    {
        return $this->layoutSize;
    }
}

class TextDisplayItem extends DisplayItem
{
    private string $text;
    private ComputedStyle $style;
    private LayoutPoint $layoutPoint;

    public function __construct(string $text, ComputedStyle $style, LayoutPoint $layoutPoint)
    {
        $this->text = $text;
        $this->style = $style;
        $this->layoutPoint = $layoutPoint;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStyle(): ComputedStyle
    {
        return $this->style;
    }

    public function getLayoutPoint(): LayoutPoint
    {
        return $this->layoutPoint;
    }
}
