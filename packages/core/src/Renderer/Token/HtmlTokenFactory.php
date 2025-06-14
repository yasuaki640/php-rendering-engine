<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Token;

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Html\Attribute;

class HtmlTokenFactory
{
    /**
     * @param Attribute[] $attributes
     */
    public static function createStartTag(string $tag, bool $selfClosing = false, array $attributes = []): StartTag
    {
        return new StartTag($tag, $selfClosing, $attributes);
    }

    public static function createEndTag(string $tag): EndTag
    {
        return new EndTag($tag);
    }

    public static function createChar(string $char): CharToken
    {
        return new CharToken($char);
    }

    public static function createEof(): EofToken
    {
        return new EofToken();
    }

    public static function createAttribute(string $name, string $value): Attribute
    {
        return new Attribute($name, $value);
    }
}
