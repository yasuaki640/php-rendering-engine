<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer;

use Yasuaki640\PhpRenderingEngine\Core\Browser;
use Yasuaki640\PhpRenderingEngine\Core\HttpResponse;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssParser;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssTokenizer;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\StyleSheet;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Api;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Window;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\HtmlParser;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\DisplayItem;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutView;

/**
 * ページクラス
 *
 * HTTPレスポンスを受信してDOMツリーを構築し、レンダリングを行う
 */
class Page
{
    private ?\WeakReference $browser = null;
    private ?Window $frame = null;
    private ?StyleSheet $style = null;
    private ?LayoutView $layoutView = null;
    /** @var DisplayItem[] */
    private array $displayItems = [];

    public function __construct()
    {
        // 初期化
    }

    /**
     * ブラウザーの弱参照を設定
     */
    public function setBrowser(Browser $browser): void
    {
        $this->browser = \WeakReference::create($browser);
    }

    /**
     * ブラウザーを取得
     */
    public function getBrowser(): ?Browser
    {
        return $this->browser?->get();
    }

    /**
     * HTTPレスポンスを受信してページを構築
     */
    public function receiveResponse(HttpResponse $response): void
    {
        $this->createFrame($response->body);
        $this->setLayoutView();
        $this->paintTree();
    }

    /**
     * HTMLからDOMフレームを作成
     */
    private function createFrame(string $html): void
    {
        $parser = new HtmlParser($html);
        $this->frame = $parser->constructTree();
        $dom = $this->frame->getDocument();

        $style = Api::getStyleContent($dom);
        $cssTokenizer = new CssTokenizer($style);
        $cssom = (new CssParser($cssTokenizer))->parseStylesheet();

        $this->style = $cssom;
    }

    /**
     * DOMフレーム（Window）を取得
     */
    public function getFrame(): ?Window
    {
        return $this->frame;
    }

    /**
     * ページがロードされているかどうかを確認
     */
    public function isLoaded(): bool
    {
        return $this->frame !== null;
    }

    /**
     * レイアウトビューを設定
     */
    private function setLayoutView(): void
    {
        $dom = $this->frame?->getDocument();
        if ($dom === null) {
            return;
        }

        $style = $this->style;
        if ($style === null) {
            return;
        }

        $this->layoutView = new LayoutView($dom, $style);
    }

    /**
     * ペイントツリーを作成
     */
    private function paintTree(): void
    {
        if ($this->layoutView !== null) {
            $this->displayItems = $this->layoutView->paint();
        }
    }

    /**
     * ディスプレイアイテムを取得
     *
     * @return DisplayItem[]
     */
    public function getDisplayItems(): array
    {
        return $this->displayItems;
    }

    /**
     * ディスプレイアイテムをクリア
     */
    public function clearDisplayItems(): void
    {
        $this->displayItems = [];
    }

    /**
     * ページをクリア
     */
    public function clear(): void
    {
        $this->frame = null;
        $this->style = null;
        $this->layoutView = null;
        $this->displayItems = [];
    }
}
