<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

enum LayoutObjectKind
{
    case Block;
    case Inline;
    case Text; // 本来はテキストもインライン要素だが、本書では話を簡単にするため、追加
}
