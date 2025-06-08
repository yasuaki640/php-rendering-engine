<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Renderer\Css;

/**
 * CSS StyleSheet representation
 * @see https://www.w3.org/TR/cssom-1/#cssstylesheet
 */
class StyleSheet
{
    /**
     * @param array<int, QualifiedRule> $rules
     */
    public function __construct(
        public array $rules = []
    ) {}

    public static function new(): self
    {
        return new self();
    }

    /**
     * @param array<int, QualifiedRule> $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }

    /**
     * @return array<int, QualifiedRule>
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function equals(StyleSheet $other): bool
    {
        if (count($this->rules) !== count($other->rules)) {
            return false;
        }

        for ($i = 0; $i < count($this->rules); $i++) {
            if (! $this->rules[$i]->equals($other->rules[$i])) {
                return false;
            }
        }

        return true;
    }
}
