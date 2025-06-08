<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

use MyApp\Core\Renderer\Dom\ElementKind;
use MyApp\Core\Renderer\Dom\Node;
use MyApp\Core\Renderer\Dom\NodeKind;

enum FontSize
{
    case Medium;
    case XLarge;
    case XXLarge;

    public static function defaultForNode(?Node $node): self
    {
        if ($node === null) {
            return self::Medium;
        }

        $nodeKind = $node->getKind();

        if ($nodeKind === NodeKind::Element) {
            $elementKind = $node->getElementKind();

            return match ($elementKind) {
                ElementKind::H1 => self::XXLarge,
                ElementKind::H2 => self::XLarge,
                default => self::Medium,
            };
        }

        return self::Medium;
    }
}
