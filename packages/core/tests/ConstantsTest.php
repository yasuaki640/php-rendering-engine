<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Constants;

class ConstantsTest extends TestCase
{
    public function testWindowDimensions(): void
    {
        $this->assertEquals(600, Constants::WINDOW_WIDTH);
        $this->assertEquals(400, Constants::WINDOW_HEIGHT);
        $this->assertEquals(5, Constants::WINDOW_PADDING);
    }

    public function testLayoutDimensions(): void
    {
        $this->assertEquals(24, Constants::TITLE_BAR_HEIGHT);
        $this->assertEquals(26, Constants::TOOLBAR_HEIGHT);
    }

    public function testComputedDimensions(): void
    {
        $expectedContentWidth = Constants::WINDOW_WIDTH - (Constants::WINDOW_PADDING * 2);
        $this->assertEquals($expectedContentWidth, Constants::CONTENT_AREA_WIDTH);

        $expectedContentHeight = Constants::WINDOW_HEIGHT - Constants::TITLE_BAR_HEIGHT - Constants::TOOLBAR_HEIGHT - (Constants::WINDOW_PADDING * 2);
        $this->assertEquals($expectedContentHeight, Constants::CONTENT_AREA_HEIGHT);
    }

    public function testCharacterDimensions(): void
    {
        $this->assertEquals(8, Constants::CHAR_WIDTH);
        $this->assertEquals(16, Constants::CHAR_HEIGHT);
        $this->assertEquals(20, Constants::CHAR_HEIGHT_WITH_PADDING);
    }
}
