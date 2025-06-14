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
