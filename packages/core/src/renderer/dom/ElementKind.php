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
    case H3 = 'h3';
    case H4 = 'h4';
    case H5 = 'h5';
    case H6 = 'h6';

    /**
     * @see https://html.spec.whatwg.org/multipage/semantics.html#the-title-element
     */
    case Title = 'title';

    /**
     * @see https://html.spec.whatwg.org/multipage/semantics.html#the-meta-element
     */
    case Meta = 'meta';

    /**
     * @see https://html.spec.whatwg.org/multipage/semantics.html#the-link-element
     */
    case Link = 'link';

    /**
     * @see https://html.spec.whatwg.org/multipage/text-level-semantics.html#the-span-element
     */
    case Span = 'span';

    /**
     * @see https://html.spec.whatwg.org/multipage/text-level-semantics.html#the-a-element
     */
    case A = 'a';

    /**
     * @see https://html.spec.whatwg.org/multipage/embedded-content.html#the-img-element
     */
    case Img = 'img';

    /**
     * @see https://html.spec.whatwg.org/multipage/text-level-semantics.html#the-br-element
     */
    case Br = 'br';

    /**
     * @see https://html.spec.whatwg.org/multipage/grouping-content.html#the-ul-element
     */
    case Ul = 'ul';

    /**
     * @see https://html.spec.whatwg.org/multipage/grouping-content.html#the-ol-element
     */
    case Ol = 'ol';

    /**
     * @see https://html.spec.whatwg.org/multipage/grouping-content.html#the-li-element
     */
    case Li = 'li';

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
            'h3' => self::H3,
            'h4' => self::H4,
            'h5' => self::H5,
            'h6' => self::H6,
            'title' => self::Title,
            'meta' => self::Meta,
            'link' => self::Link,
            'span' => self::Span,
            'a' => self::A,
            'img' => self::Img,
            'br' => self::Br,
            'ul' => self::Ul,
            'ol' => self::Ol,
            'li' => self::Li,
            'div' => self::Div,
            default => throw new \InvalidArgumentException("Unimplemented element name: {$tagName}"),
        };
    }
}
