<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout;

class ComputedStyle
{
    private ?Color $backgroundColor;
    private ?Color $color;
    private ?DisplayType $display;
    private ?FontSize $fontSize;
    private ?TextDecoration $textDecoration;
    private ?float $height;
    private ?float $width;

    public function __construct()
    {
        $this->backgroundColor = null;
        $this->color = null;
        $this->display = null;
        $this->fontSize = null;
        $this->textDecoration = null;
        $this->height = null;
        $this->width = null;
    }

    public function defaulting(?object $node, ?ComputedStyle $parentStyle): void
    {
        // もし親ノードが存在し、親のCSSの値が初期値とは異なる場合、値を継承する
        if ($parentStyle !== null) {
            if ($this->backgroundColor === null && ! $parentStyle->getBackgroundColor()->equals(Color::white())) {
                $this->backgroundColor = $parentStyle->getBackgroundColor();
            }
            if ($this->color === null && ! $parentStyle->getColor()->equals(Color::black())) {
                $this->color = $parentStyle->getColor();
            }
            if ($this->fontSize === null && $parentStyle->getFontSize() !== FontSize::Medium) {
                $this->fontSize = $parentStyle->getFontSize();
            }
            if ($this->textDecoration === null && $parentStyle->getTextDecoration() !== TextDecoration::None) {
                $this->textDecoration = $parentStyle->getTextDecoration();
            }
        }

        // 各プロパティに対して、初期値を設定する
        if ($this->backgroundColor === null) {
            $this->backgroundColor = Color::white();
        }
        if ($this->color === null) {
            $this->color = Color::black();
        }
        if ($this->display === null) {
            $this->display = DisplayType::defaultForNode($node);
        }
        if ($this->fontSize === null) {
            $this->fontSize = FontSize::defaultForNode($node);
        }
        if ($this->textDecoration === null) {
            $this->textDecoration = TextDecoration::defaultForNode($node);
        }
        if ($this->height === null) {
            $this->height = 0.0;
        }
        if ($this->width === null) {
            $this->width = 0.0;
        }
    }

    public function setBackgroundColor(Color $color): void
    {
        $this->backgroundColor = $color;
    }

    public function getBackgroundColor(): Color
    {
        if ($this->backgroundColor === null) {
            throw new \RuntimeException('failed to access CSS property: background_color');
        }

        return $this->backgroundColor;
    }

    public function setColor(Color $color): void
    {
        $this->color = $color;
    }

    public function getColor(): Color
    {
        if ($this->color === null) {
            throw new \RuntimeException('failed to access CSS property: color');
        }

        return $this->color;
    }

    public function setDisplay(DisplayType $display): void
    {
        $this->display = $display;
    }

    public function getDisplay(): DisplayType
    {
        if ($this->display === null) {
            throw new \RuntimeException('failed to access CSS property: display');
        }

        return $this->display;
    }

    public function getFontSize(): FontSize
    {
        if ($this->fontSize === null) {
            throw new \RuntimeException('failed to access CSS property: font_size');
        }

        return $this->fontSize;
    }

    public function setTextDecoration(TextDecoration $textDecoration): void
    {
        $this->textDecoration = $textDecoration;
    }

    public function getTextDecoration(): TextDecoration
    {
        if ($this->textDecoration === null) {
            throw new \RuntimeException('failed to access CSS property: text_decoration');
        }

        return $this->textDecoration;
    }

    public function setHeight(float $height): void
    {
        $this->height = $height;
    }

    public function getHeight(): float
    {
        if ($this->height === null) {
            throw new \RuntimeException('failed to access CSS property: height');
        }

        return $this->height;
    }

    public function setWidth(float $width): void
    {
        $this->width = $width;
    }

    public function getWidth(): float
    {
        if ($this->width === null) {
            throw new \RuntimeException('failed to access CSS property: width');
        }

        return $this->width;
    }
}
