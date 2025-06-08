<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Renderer\Css;

/**
 * CSS Declaration representation
 * @see https://www.w3.org/TR/css-syntax-3/#declaration
 */
class Declaration
{
    public function __construct(
        public string $property = '',
        public CssToken $value = new CssToken(CssTokenType::Ident, '')
    ) {}

    public static function new(): self
    {
        return new self();
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    public function setValue(CssToken $value): void
    {
        $this->value = $value;
    }

    public function equals(Declaration $other): bool
    {
        return $this->property === $other->property && 
               $this->value->equals($other->value);
    }
}
