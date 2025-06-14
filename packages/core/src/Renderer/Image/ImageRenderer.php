<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Image;

use Yasuaki640\PhpRenderingEngine\Core\Constants;
use Yasuaki640\PhpRenderingEngine\Core\DisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\RectDisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\FontSize;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\TextDecoration;
use Yasuaki640\PhpRenderingEngine\Core\TextDisplayItem;

/**
 * GDライブラリを使用して画像にレンダリングするクラス
 */
class ImageRenderer
{
    private \GdImage $image;
    private int $windowWidth;
    private int $windowHeight;
    private int $toolbarHeight;
    private int $windowPadding;

    /**
     * @param int $windowWidth
     * @param int $windowHeight
     * @param int $toolbarHeight
     * @param int $windowPadding
     */
    public function __construct(
        int $windowWidth = Constants::WINDOW_WIDTH,
        int $windowHeight = Constants::WINDOW_HEIGHT,
        int $toolbarHeight = Constants::TOOLBAR_HEIGHT,
        int $windowPadding = Constants::WINDOW_PADDING
    ) {
        $this->windowWidth = $windowWidth;
        $this->windowHeight = $windowHeight;
        $this->toolbarHeight = $toolbarHeight;
        $this->windowPadding = $windowPadding;

        // GD画像リソースを作成
        $this->image = imagecreatetruecolor($windowWidth, $windowHeight);

        // 背景を白に設定
        $white = imagecolorallocate($this->image, 255, 255, 255);
        imagefill($this->image, 0, 0, $white);
    }

    /**
     * アドレスバーを描画する
     *
     * @param string $url
     */
    public function drawAddressBar(string $url): void
    {
        // ツールバーの背景色（薄いグレー）
        $lightGrey = imagecolorallocate($this->image, 211, 211, 211);
        $grey = imagecolorallocate($this->image, 128, 128, 128);
        $darkGrey = imagecolorallocate($this->image, 90, 90, 90);
        $black = imagecolorallocate($this->image, 0, 0, 0);
        $white = imagecolorallocate($this->image, 255, 255, 255);

        // ツールバーの背景を描画
        imagefilledrectangle(
            $this->image,
            0,
            0,
            $this->windowWidth,
            $this->toolbarHeight,
            $lightGrey
        );

        // ツールバーとコンテンツエリアの境界線
        imageline(
            $this->image,
            0,
            $this->toolbarHeight,
            $this->windowWidth - 1,
            $this->toolbarHeight,
            $grey
        );

        imageline(
            $this->image,
            0,
            $this->toolbarHeight + 1,
            $this->windowWidth - 1,
            $this->toolbarHeight + 1,
            $darkGrey
        );

        // "Address:"ラベルを描画
        imagestring($this->image, 3, 5, 5, 'Address:', $black);

        // アドレスバーの矩形を描画
        $addressBarX = 70;
        $addressBarY = 2;
        $addressBarWidth = $this->windowWidth - 74;
        $addressBarHeight = Constants::ADDRESSBAR_HEIGHT;

        imagefilledrectangle(
            $this->image,
            $addressBarX,
            $addressBarY,
            $addressBarX + $addressBarWidth,
            $addressBarY + $addressBarHeight,
            $white
        );

        // アドレスバーの境界線
        imagerectangle(
            $this->image,
            $addressBarX,
            $addressBarY,
            $addressBarX + $addressBarWidth,
            $addressBarY + $addressBarHeight,
            $grey
        );

        // URLテキストを描画
        if (! empty($url)) {
            imagestring($this->image, 3, $addressBarX + 2, $addressBarY + 4, $url, $black);
        }
    }

    /**
     * DisplayItemのリストを描画する
     *
     * @param DisplayItem[] $displayItems
     */
    public function renderDisplayItems(array $displayItems): void
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
     * DisplayItemのリストを描画する（互換性のため）
     *
     * @param DisplayItem[] $displayItems
     */
    public function render(array $displayItems): void
    {
        // アドレスバーを描画
        $this->drawAddressBar("http://localhost/test.html");
        $this->renderDisplayItems($displayItems);
    }

    /**
     * テキストアイテムを描画する
     *
     * @param TextDisplayItem $item
     */
    private function renderText(TextDisplayItem $item): void
    {
        $style = $item->getStyle();
        $point = $item->getLayoutPoint();
        $text = $item->getText();

        // 色を取得
        $color = $this->allocateColor($style->getColor());

        // フォントサイズを取得
        $fontId = $this->convertFontSize($style->getFontSize());

        // テキストの描画位置を計算
        $x = $point->getX() + $this->windowPadding;
        $y = $point->getY() + $this->windowPadding + $this->toolbarHeight;

        // テキストを描画
        imagestring($this->image, $fontId, $x, $y, $text, $color);

        // 下線が必要な場合は描画
        if ($style->getTextDecoration() === TextDecoration::Underline) {
            $textWidth = strlen($text) * imagefontwidth($fontId);
            $underlineY = $y + imagefontheight($fontId);
            imageline($this->image, $x, $underlineY, $x + $textWidth, $underlineY, $color);
        }
    }

    /**
     * 矩形アイテムを描画する
     *
     * @param RectDisplayItem $item
     */
    private function renderRect(RectDisplayItem $item): void
    {
        $style = $item->getStyle();
        $point = $item->getLayoutPoint();
        $size = $item->getLayoutSize();

        // 背景色を取得
        $backgroundColor = $this->allocateColor($style->getBackgroundColor());

        // 矩形の描画位置を計算
        $x = $point->getX() + $this->windowPadding;
        $y = $point->getY() + $this->windowPadding + $this->toolbarHeight;

        // 矩形を描画
        imagefilledrectangle(
            $this->image,
            $x,
            $y,
            $x + $size->getWidth(),
            $y + $size->getHeight(),
            $backgroundColor
        );
    }

    /**
     * Colorオブジェクトから色を割り当てる
     *
     * @param \Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\Color $color
     * @return int
     */
    private function allocateColor(\Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\Color $color): int
    {
        $code = $color->getCodeAsInt();
        $r = ($code >> 16) & 0xFF;
        $g = ($code >> 8) & 0xFF;
        $b = $code & 0xFF;

        return imagecolorallocate($this->image, $r, $g, $b);
    }

    /**
     * FontSizeをGDのフォントIDに変換する
     *
     * @param FontSize $fontSize
     * @return int
     */
    private function convertFontSize(FontSize $fontSize): int
    {
        return match ($fontSize) {
            FontSize::Medium => 3,
            FontSize::XLarge => 4,
            FontSize::XXLarge => 5,
        };
    }

    /**
     * 画像をファイルに保存する
     *
     * @param string $filename
     * @return bool
     */
    public function saveToFile(string $filename): bool
    {
        // 拡張子に基づいて適切な関数を使用
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'png':
                return imagepng($this->image, $filename);
            case 'jpg':
            case 'jpeg':
                return imagejpeg($this->image, $filename);
            case 'gif':
                return imagegif($this->image, $filename);
            default:
                return imagepng($this->image, $filename);
        }
    }

    /**
     * 画像を出力する
     *
     * @param string $type
     */
    public function output(string $type = 'png'): void
    {
        switch ($type) {
            case 'png':
                header('Content-Type: image/png');
                imagepng($this->image);

                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                imagejpeg($this->image);

                break;
            case 'gif':
                header('Content-Type: image/gif');
                imagegif($this->image);

                break;
            default:
                header('Content-Type: image/png');
                imagepng($this->image);

                break;
        }
    }

    /**
     * リソースを破棄する
     */
    public function __destruct()
    {
        if (isset($this->image)) {
            imagedestroy($this->image);
        }
    }
}
