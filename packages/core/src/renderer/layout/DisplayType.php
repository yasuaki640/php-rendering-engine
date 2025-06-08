<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

use MyApp\Core\Exception\UnexpectedInputException;
use MyApp\Core\Renderer\Dom\Node;
use MyApp\Core\Renderer\Dom\NodeKind;

enum DisplayType
{
    case Block;
    case Inline;
    case DisplayNone;

    public static function defaultForNode(?Node $node): self
    {
        if ($node === null) {
            return self::Block;
        }

        $nodeKind = $node->getKind();

        return match ($nodeKind) {
            NodeKind::Document => self::Block,
            NodeKind::Element => $node->getElement()?->isBlockElement() ? self::Block : self::Inline,
            NodeKind::Text => self::Inline,
        };
    }

    public static function fromString(string $value): self
    {
        return match ($value) {
            'block' => self::Block,
            'inline' => self::Inline,
            'none' => self::DisplayNone,
            default => throw new UnexpectedInputException("display '$value' is not supported yet"),
        };
    }
}
