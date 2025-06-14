<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer;

use GdImage;
use Yasuaki640\PhpRenderingEngine\Core\Constants;
use Yasuaki640\PhpRenderingEngine\Core\DisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\RectDisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\Color;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\FontSize;
use Yasuaki640\PhpRenderingEngine\Core\TextDisplayItem;

/**
 * GDライブラリを使ってHTMLを画像に描画するレンダラー
 */
class ImageRenderer
{
    private GdImage $image;
    private int $width;
    private int $height;
    private int $backgroundColor;

    /**
     * @param int $width 画像の幅
     * @param int $height 画像の高さ
     */
    public function __construct(int $width = 800, int $height = 600)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreate($width, $height);

        // 背景色を白に設定
        $this->backgroundColor = imagecolorallocate($this->image, 255, 255, 255);
    }

    /**
     * DisplayItemのリストを画像に描画
     *
     * @param DisplayItem[] $displayItems
     * @return void
     */
    public function render(array $displayItems): void
    {
        foreach ($displayItems as $item) {
            if ($item instanceof TextDisplayItem) {
                $this->renderText($item);
            } elseif ($item instanceof RectDisplayItem) {
                $this->renderRect($item);
            }
        }
    }

    /**
     * テキストを描画
     *
     * @param TextDisplayItem $item
     * @return void
     */
    private function renderText(TextDisplayItem $item): void
    {
        $color = $this->convertColor($item->getStyle()->getColor());
        $fontSize = $this->convertFontSize($item->getStyle()->getFontSize());

        $x = $item->getLayoutPoint()->getX() + Constants::WINDOW_PADDING;
        $y = $item->getLayoutPoint()->getY() + Constants::WINDOW_PADDING + $fontSize;

        // フォントサイズに応じて適切な関数を選択
        if ($fontSize <= 12) {
            imagestring($this->image, 3, $x, $y - 12, $item->getText(), $color);
        } else {
            imagestring($this->image, 5, $x, $y - 15, $item->getText(), $color);
        }
    }

    /**
     * 矩形を描画
     *
     * @param RectDisplayItem $item
     * @return void
     */
    private function renderRect(RectDisplayItem $item): void
    {
        $backgroundColor = $this->convertColor($item->getStyle()->getBackgroundColor());

        $x1 = $item->getLayoutPoint()->getX() + Constants::WINDOW_PADDING;
        $y1 = $item->getLayoutPoint()->getY() + Constants::WINDOW_PADDING;
        $x2 = $x1 + $item->getLayoutSize()->getWidth();
        $y2 = $y1 + $item->getLayoutSize()->getHeight();

        imagefilledrectangle($this->image, $x1, $y1, $x2, $y2, $backgroundColor);
    }

    /**
     * Colorオブジェクトを画像の色に変換
     *
     * @param Color $color
     * @return int
     */
    private function convertColor(Color $color): int
    {
        $hex = $color->getCodeAsInt();
        $r = ($hex >> 16) & 0xFF;
        $g = ($hex >> 8) & 0xFF;
        $b = $hex & 0xFF;

        return imagecolorallocate($this->image, $r, $g, $b);
    }

    /**
     * FontSizeを画像用のサイズに変換
     *
     * @param FontSize $fontSize
     * @return int
     */
    private function convertFontSize(FontSize $fontSize): int
    {
        return match ($fontSize) {
            FontSize::Medium => 12,
            FontSize::XLarge => 18,
            FontSize::XXLarge => 24,
            default => 12,
        };
    }

    /**
     * 画像をファイルに保存
     *
     * @param string $filename
     * @return bool
     */
    public function saveToFile(string $filename): bool
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match ($extension) {
            'png' => imagepng($this->image, $filename),
            'jpg', 'jpeg' => imagejpeg($this->image, $filename),
            'gif' => imagegif($this->image, $filename),
            default => imagepng($this->image, $filename),
        };
    }

    /**
     * 画像をブラウザに出力
     *
     * @param string $format
     * @return void
     */
    public function output(string $format = 'png'): void
    {
        header("Content-Type: image/{$format}");

        match ($format) {
            'png' => imagepng($this->image),
            'jpg', 'jpeg' => imagejpeg($this->image),
            'gif' => imagegif($this->image),
            default => imagepng($this->image),
        };
    }

    /**
     * リソースを解放
     */
    public function __destruct()
    {
        if (isset($this->image)) {
            imagedestroy($this->image);
        }
    }
}
