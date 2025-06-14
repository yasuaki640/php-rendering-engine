<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core;

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\ComputedStyle;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutPoint;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutSize;

/**
 * 表示項目を表す基底クラス
 */
abstract class DisplayItem
{
    /**
     * @param ComputedStyle $style
     * @param LayoutPoint $layoutPoint
     */
    public function __construct(
        protected ComputedStyle $style,
        protected LayoutPoint $layoutPoint
    ) {}

    /**
     * @return ComputedStyle
     */
    public function getStyle(): ComputedStyle
    {
        return $this->style;
    }

    /**
     * @return LayoutPoint
     */
    public function getLayoutPoint(): LayoutPoint
    {
        return $this->layoutPoint;
    }

    /**
     * テキスト表示項目を作成するファクトリーメソッド
     *
     * @param string $text
     * @param ComputedStyle $style
     * @param LayoutPoint $layoutPoint
     * @return TextDisplayItem
     */
    public static function createText(string $text, ComputedStyle $style, LayoutPoint $layoutPoint): TextDisplayItem
    {
        return new TextDisplayItem($text, $style, $layoutPoint);
    }

    /**
     * 矩形表示項目を作成するファクトリーメソッド
     *
     * @param ComputedStyle $style
     * @param LayoutPoint $layoutPoint
     * @param LayoutSize $layoutSize
     * @return RectDisplayItem
     */
    public static function createRect(ComputedStyle $style, LayoutPoint $layoutPoint, LayoutSize $layoutSize): RectDisplayItem
    {
        return new RectDisplayItem($style, $layoutPoint, $layoutSize);
    }
}

/**
 * 矩形の表示項目
 */
class RectDisplayItem extends DisplayItem
{
    /**
     * @param ComputedStyle $style
     * @param LayoutPoint $layoutPoint
     * @param LayoutSize $layoutSize
     */
    public function __construct(
        ComputedStyle $style,
        LayoutPoint $layoutPoint,
        private LayoutSize $layoutSize
    ) {
        parent::__construct($style, $layoutPoint);
    }

    /**
     * @return LayoutSize
     */
    public function getLayoutSize(): LayoutSize
    {
        return $this->layoutSize;
    }
}

/**
 * テキストの表示項目
 */
class TextDisplayItem extends DisplayItem
{
    /**
     * @param string $text
     * @param ComputedStyle $style
     * @param LayoutPoint $layoutPoint
     */
    public function __construct(
        private string $text,
        ComputedStyle $style,
        LayoutPoint $layoutPoint
    ) {
        parent::__construct($style, $layoutPoint);
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
