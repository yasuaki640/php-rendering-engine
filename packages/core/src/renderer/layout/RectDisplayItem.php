<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

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
