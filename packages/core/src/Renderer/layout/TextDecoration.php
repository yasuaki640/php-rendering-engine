<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout;

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\ElementKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;

enum TextDecoration
{
    case None;
    case Underline;

    public static function defaultForNode(?Node $node): self
    {
        if ($node === null) {
            return self::None;
        }

        $nodeKind = $node->getKind();

        if ($nodeKind === NodeKind::Element) {
            $elementKind = $node->getElementKind();

            if ($elementKind === ElementKind::A) {
                return self::Underline;
            }
        }

        return self::None;
    }
}
