<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Browser;

class BrowserTest extends TestCase
{
    public function testCreateBrowser(): void
    {
        $browser = new Browser();

        $this->assertEquals(1, $browser->getPageCount());
        $this->assertNotNull($browser->getCurrentPage());
    }

    public function testGetCurrentPage(): void
    {
        $browser = new Browser();
        $currentPage = $browser->getCurrentPage();

        $this->assertNotNull($currentPage);
        $this->assertSame($browser, $currentPage->getBrowser());
    }

    public function testAddPage(): void
    {
        $browser = new Browser();
        $initialPageCount = $browser->getPageCount();

        $newPage = $browser->addPage();

        $this->assertEquals($initialPageCount + 1, $browser->getPageCount());
        $this->assertNotNull($newPage);
        $this->assertSame($browser, $newPage->getBrowser());
    }

    public function testSetActivePageIndex(): void
    {
        $browser = new Browser();
        $page2 = $browser->addPage();

        // 2番目のページをアクティブに設定
        $browser->setActivePageIndex(1);

        $this->assertSame($page2, $browser->getCurrentPage());
    }

    public function testSetActivePageIndexWithInvalidIndex(): void
    {
        $browser = new Browser();
        $originalPage = $browser->getCurrentPage();

        // 無効なインデックスを設定しても変更されない
        $browser->setActivePageIndex(99);

        $this->assertSame($originalPage, $browser->getCurrentPage());
    }

    public function testGetPages(): void
    {
        $browser = new Browser();
        $page2 = $browser->addPage();
        $page3 = $browser->addPage();

        $pages = $browser->getPages();

        $this->assertCount(3, $pages);
        $this->assertContains($page2, $pages);
        $this->assertContains($page3, $pages);
    }
}
