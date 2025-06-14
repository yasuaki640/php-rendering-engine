<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

/**
 * Display item enum corresponding to display_item.rs in the Rust implementation.
 * Represents visual elements that can be rendered.
 */
abstract class DisplayItem
{
    /**
     * Create a rectangular display item.
     */
    public static function rect(ComputedStyle $style, LayoutPoint $layoutPoint, LayoutSize $layoutSize): RectDisplayItem
    {
        return new RectDisplayItem($style, $layoutPoint, $layoutSize);
    }

    /**
     * Create a text display item.
     */
    public static function text(string $text, ComputedStyle $style, LayoutPoint $layoutPoint): TextDisplayItem
    {
        return new TextDisplayItem($text, $style, $layoutPoint);
    }

    /**
     * Create a rectangular display item (legacy method name).
     * @deprecated Use rect() instead
     */
    public static function createRect(ComputedStyle $style, LayoutPoint $layoutPoint, LayoutSize $layoutSize): RectDisplayItem
    {
        return self::rect($style, $layoutPoint, $layoutSize);
    }

    /**
     * Create a text display item (legacy method name).
     * @deprecated Use text() instead
     */
    public static function createText(string $text, ComputedStyle $style, LayoutPoint $layoutPoint): TextDisplayItem
    {
        return self::text($text, $style, $layoutPoint);
    }

    /**
     * Get the computed style for this display item.
     */
    abstract public function getStyle(): ComputedStyle;

    /**
     * Get the layout point for this display item.
     */
    abstract public function getLayoutPoint(): LayoutPoint;
}
