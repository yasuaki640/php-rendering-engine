<?php

declare(strict_types=1);

namespace MyApp\Core;

use MyApp\Core\Renderer\Page;

/**
 * ブラウザークラス
 *
 * 複数のページを管理し、現在のアクティブなページを追跡する
 */
class Browser
{
    private int $activePageIndex = 0;
    /** @var Page[] */
    private array $pages = [];

    public function __construct()
    {
        // 初期ページを作成
        $page = new Page();
        $page->setBrowser($this);
        $this->pages[] = $page;
    }

    /**
     * 現在のアクティブなページを取得
     */
    public function getCurrentPage(): Page
    {
        return $this->pages[$this->activePageIndex];
    }

    /**
     * 新しいページを追加
     */
    public function addPage(): Page
    {
        $page = new Page();
        $page->setBrowser($this);
        $this->pages[] = $page;

        return $page;
    }

    /**
     * アクティブなページのインデックスを設定
     */
    public function setActivePageIndex(int $index): void
    {
        if ($index >= 0 && $index < count($this->pages)) {
            $this->activePageIndex = $index;
        }
    }

    /**
     * すべてのページを取得
     *
     * @return Page[]
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * ページ数を取得
     */
    public function getPageCount(): int
    {
        return count($this->pages);
    }
}
