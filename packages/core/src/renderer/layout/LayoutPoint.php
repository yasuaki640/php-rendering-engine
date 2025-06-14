<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout;

class LayoutPoint
{
    private int $x;
    private int $y;

    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function setX(int $x): void
    {
        $this->x = $x;
    }

    public function setY(int $y): void
    {
        $this->y = $y;
    }

    public function equals(LayoutPoint $other): bool
    {
        return $this->x === $other->x && $this->y === $other->y;
    }

    public function __toString(): string
    {
        return "LayoutPoint({$this->x}, {$this->y})";
    }
}
