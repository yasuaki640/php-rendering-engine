<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Dom;

/**
 * DOMノードの種類を表すenum
 */
enum NodeKind
{
    /**
     * @see https://dom.spec.whatwg.org/#interface-document
     */
    case Document;

    /**
     * @see https://dom.spec.whatwg.org/#interface-element
     */
    case Element;

    /**
     * @see https://dom.spec.whatwg.org/#interface-text
     */
    case Text;
}
