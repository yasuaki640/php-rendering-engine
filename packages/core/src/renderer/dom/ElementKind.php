<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Dom;

/**
 * HTML要素の種類を表すenum
 *
 * @see https://dom.spec.whatwg.org/#interface-element
 */
enum ElementKind: string
{
    /**
     * @see https://html.spec.whatwg.org/multipage/semantics.html#the-html-element
     */
    case Html = 'html';

    /**
     * @see https://html.spec.whatwg.org/multipage/semantics.html#the-head-element
     */
    case Head = 'head';

    /**
     * @see https://html.spec.whatwg.org/multipage/semantics.html#the-style-element
     */
    case Style = 'style';

    /**
     * @see https://html.spec.whatwg.org/multipage/scripting.html#the-script-element
     */
    case Script = 'script';

    /**
     * @see https://html.spec.whatwg.org/multipage/sections.html#the-body-element
     */
    case Body = 'body';

    /**
     * @see https://html.spec.whatwg.org/multipage/grouping-content.html#the-p-element
     */
    case P = 'p';

    /**
     * @see https://html.spec.whatwg.org/multipage/sections.html#the-h1,-h2,-h3,-h4,-h5,-and-h6-elements
     */
    case H1 = 'h1';
    case H2 = 'h2';

    /**
     * @see https://html.spec.whatwg.org/multipage/text-level-semantics.html#the-a-element
     */
    case A = 'a';

    /**
     * @see https://html.spec.whatwg.org/multipage/grouping-content.html#the-div-element
     */
    case Div = 'div';

    /**
     * 文字列からElementKindを作成
     */
    public static function fromString(string $tagName): self
    {
        return match ($tagName) {
            'html' => self::Html,
            'head' => self::Head,
            'style' => self::Style,
            'script' => self::Script,
            'body' => self::Body,
            'p' => self::P,
            'h1' => self::H1,
            'h2' => self::H2,
            'a' => self::A,
            'div' => self::Div,
            default => throw new \InvalidArgumentException("Unimplemented element name: {$tagName}"),
        };
    }
}
