<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Css;

/**
 * CSS Qualified Rule representation
 * @see https://www.w3.org/TR/css-syntax-3/#qualified-rule
 */
class QualifiedRule
{
    /**
     * @param Selector $selector The selector for this rule
     * @param array<Declaration> $declarations List of CSS declarations
     */
    public function __construct(
        public Selector $selector,
        public array $declarations = []
    ) {}

    public static function new(): self
    {
        return new self(
            new Selector(SelectorType::TypeSelector, '')
        );
    }

    public function setSelector(Selector $selector): void
    {
        $this->selector = $selector;
    }

    /**
     * @param array<Declaration> $declarations
     */
    public function setDeclarations(array $declarations): void
    {
        $this->declarations = $declarations;
    }

    /**
     * @return array<Declaration>
     */
    public function getDeclarations(): array
    {
        return $this->declarations;
    }

    public function getSelector(): Selector
    {
        return $this->selector;
    }

    public function equals(QualifiedRule $other): bool
    {
        if (! $this->selector->equals($other->selector)) {
            return false;
        }

        if (count($this->declarations) !== count($other->declarations)) {
            return false;
        }

        for ($i = 0; $i < count($this->declarations); $i++) {
            if (! $this->declarations[$i]->equals($other->declarations[$i])) {
                return false;
            }
        }

        return true;
    }
}
