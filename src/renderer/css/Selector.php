<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Renderer\Css;

/**
 * CSS Selector types
 * @see https://www.w3.org/TR/selectors-4/
 */
enum SelectorType
{
    /**
     * Type Selector (e.g., div, p, h1)
     * @see https://www.w3.org/TR/selectors-4/#type-selectors
     */
    case TypeSelector;

    /**
     * Class Selector (e.g., .class-name)
     * @see https://www.w3.org/TR/selectors-4/#class-html
     */
    case ClassSelector;

    /**
     * ID Selector (e.g., #id-name)
     * @see https://www.w3.org/TR/selectors-4/#id-selectors
     */
    case IdSelector;

    /**
     * Unknown Selector (used when parsing errors occur)
     */
    case UnknownSelector;
}

/**
 * CSS Selector representation
 * @see https://www.w3.org/TR/selectors-4/
 */
class Selector
{
    public function __construct(
        public SelectorType $type,
        public string $value
    ) {}

    public static function typeSelector(string $value): self
    {
        return new self(SelectorType::TypeSelector, $value);
    }

    public static function classSelector(string $value): self
    {
        return new self(SelectorType::ClassSelector, $value);
    }

    public static function idSelector(string $value): self
    {
        return new self(SelectorType::IdSelector, $value);
    }

    public static function unknownSelector(): self
    {
        return new self(SelectorType::UnknownSelector, '');
    }

    public function equals(Selector $other): bool
    {
        return $this->type === $other->type && $this->value === $other->value;
    }
}
