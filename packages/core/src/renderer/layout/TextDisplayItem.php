<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

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
