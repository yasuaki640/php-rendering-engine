<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Layout;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout\LayoutConstants;

class LayoutConstantsTest extends TestCase
{
    public function testWindowDimensions(): void
    {
        $this->assertEquals(600, LayoutConstants::WINDOW_WIDTH);
        $this->assertEquals(400, LayoutConstants::WINDOW_HEIGHT);
        $this->assertEquals(5, LayoutConstants::WINDOW_PADDING);
    }

    public function testBarHeights(): void
    {
        $this->assertEquals(24, LayoutConstants::TITLE_BAR_HEIGHT);
        $this->assertEquals(26, LayoutConstants::TOOLBAR_HEIGHT);
    }

    public function testContentAreaDimensions(): void
    {
        $expectedWidth = LayoutConstants::WINDOW_WIDTH - LayoutConstants::WINDOW_PADDING * 2;
        $expectedHeight = LayoutConstants::WINDOW_HEIGHT
            - LayoutConstants::TITLE_BAR_HEIGHT
            - LayoutConstants::TOOLBAR_HEIGHT
            - LayoutConstants::WINDOW_PADDING * 2;

        $this->assertEquals($expectedWidth, LayoutConstants::CONTENT_AREA_WIDTH);
        $this->assertEquals($expectedHeight, LayoutConstants::CONTENT_AREA_HEIGHT);
        $this->assertEquals(590, LayoutConstants::CONTENT_AREA_WIDTH);
        $this->assertEquals(340, LayoutConstants::CONTENT_AREA_HEIGHT);
    }

    public function testCharacterDimensions(): void
    {
        $this->assertEquals(8, LayoutConstants::CHAR_WIDTH);
        $this->assertEquals(16, LayoutConstants::CHAR_HEIGHT);
        $this->assertEquals(20, LayoutConstants::CHAR_HEIGHT_WITH_PADDING);
    }
}
