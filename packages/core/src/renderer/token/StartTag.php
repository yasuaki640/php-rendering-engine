<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

use MyApp\Core\Renderer\Attribute;

class StartTag implements HtmlToken
{
    /**
     * @param Attribute[] $attributes
     */
    public function __construct(
        public readonly string $tag,
        public readonly bool $selfClosing,
        public readonly array $attributes = []
    ) {}

    public function getType(): string
    {
        return 'StartTag';
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function isSelfClosing(): bool
    {
        return $this->selfClosing;
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
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
}
