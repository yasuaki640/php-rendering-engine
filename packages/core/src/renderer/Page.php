<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer;

use MyApp\Core\Browser;
use MyApp\Core\HttpResponse;
use MyApp\Core\Renderer\Dom\Window;
use MyApp\Core\Renderer\Html\HtmlParser;

/**
 * ページクラス
 *
 * HTTPレスポンスを受信してDOMツリーを構築し、レンダリングを行う
 */
class Page
{
    private ?\WeakReference $browser = null;
    private ?Window $frame = null;

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
    }

    /**
     * HTMLからDOMフレームを作成
     */
    private function createFrame(string $html): void
    {
        $parser = new HtmlParser($html);
        $this->frame = $parser->constructTree();
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
     * ページをクリア
     */
    public function clear(): void
    {
        $this->frame = null;
    }
}
