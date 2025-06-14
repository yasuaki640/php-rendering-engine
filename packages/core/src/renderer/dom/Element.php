<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Dom;

use MyApp\Core\Renderer\Html\Attribute;

/**
 * DOM Element
 *
 * @see https://dom.spec.whatwg.org/#interface-element
 */
class Element
{
    private ElementKind $kind;
    /** @var Attribute[] */
    private array $attributes;

    /**
     * @param Attribute[] $attributes
     */
    public function __construct(string $elementName, array $attributes = [])
    {
        $this->kind = ElementKind::fromString($elementName);
        $this->attributes = $attributes;
    }

    public function getKind(): ElementKind
    {
        return $this->kind;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param Attribute[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function getAttribute(string $name): ?Attribute
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->name === $name) {
                return $attribute;
            }
        }

        return null;
    }

    public function hasAttribute(string $name): bool
    {
        return $this->getAttribute($name) !== null;
    }

    public function addAttribute(Attribute $attribute): void
    {
        $this->attributes[] = $attribute;
    }

    /**
     * ブロック要素かどうかを判定
     */
    public function isBlockElement(): bool
    {
        return match ($this->kind) {
            ElementKind::Body,
            ElementKind::H1,
            ElementKind::H2,
            ElementKind::P => true,
            default => false,
        };
    }
}
