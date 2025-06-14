<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core;

/**
 * Constants for layout and window dimensions.
 * Corresponds to constants.rs in the Rust implementation.
 */
class Constants
{
    public const WINDOW_WIDTH = 600;
    public const WINDOW_HEIGHT = 400;
    public const WINDOW_PADDING = 5;

    public const TITLE_BAR_HEIGHT = 24;

    public const TOOLBAR_HEIGHT = 26;

    public const ADDRESSBAR_HEIGHT = 20;

    public const CONTENT_AREA_WIDTH = self::WINDOW_WIDTH - (self::WINDOW_PADDING * 2);
    public const CONTENT_AREA_HEIGHT = self::WINDOW_HEIGHT - self::TITLE_BAR_HEIGHT - self::TOOLBAR_HEIGHT - (self::WINDOW_PADDING * 2);

    public const CHAR_WIDTH = 8;
    public const CHAR_HEIGHT = 16;
    public const CHAR_HEIGHT_WITH_PADDING = self::CHAR_HEIGHT + 4;
}
