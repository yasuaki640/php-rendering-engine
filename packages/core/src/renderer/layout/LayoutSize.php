<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout;

class LayoutSize
{
    private int $width;
    private int $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function equals(LayoutSize $other): bool
    {
        return $this->width === $other->width && $this->height === $other->height;
    }

    public function __toString(): string
    {
        return "LayoutSize({$this->width}, {$this->height})";
    }
}
