<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

class LayoutConstants
{
    public const WINDOW_WIDTH = 600;
    public const WINDOW_HEIGHT = 400;
    public const WINDOW_PADDING = 5;

    // noliライブラリに定義されている定数
    public const TITLE_BAR_HEIGHT = 24;

    public const TOOLBAR_HEIGHT = 26;

    public const CONTENT_AREA_WIDTH = self::WINDOW_WIDTH - self::WINDOW_PADDING * 2;
    public const CONTENT_AREA_HEIGHT = self::WINDOW_HEIGHT - self::TITLE_BAR_HEIGHT - self::TOOLBAR_HEIGHT - self::WINDOW_PADDING * 2;

    public const CHAR_WIDTH = 8;
    public const CHAR_HEIGHT = 16;
    public const CHAR_HEIGHT_WITH_PADDING = self::CHAR_HEIGHT + 4;
}
