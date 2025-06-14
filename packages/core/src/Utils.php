<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core;

use Yasuaki640\PhpRenderingEngine\Core\renderer\dom\Node;

/**
 * Utility functions for DOM manipulation and debugging.
 * Corresponds to utils.rs in the Rust implementation.
 */
class Utils
{
    /**
     * Convert DOM tree to string representation for debugging.
     */
    public static function convertDomToString(?Node $root): string
    {
        $result = "\n";
        self::convertDomToStringInternal($root, 0, $result);

        return $result;
    }

    /**
     * Internal recursive function for DOM to string conversion.
     */
    private static function convertDomToStringInternal(?Node $node, int $depth, string &$result): void
    {
        if ($node === null) {
            return;
        }

        $result .= str_repeat('  ', $depth);
        $result .= $node->getKind()->name;
        $result .= "\n";

        self::convertDomToStringInternal($node->getFirstChild(), $depth + 1, $result);
        self::convertDomToStringInternal($node->getNextSibling(), $depth, $result);
    }
}
