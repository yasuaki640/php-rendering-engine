<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout;

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\ElementKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;

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
