<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer;

class Attribute
{
    public function __construct(
        public readonly string $name,
        public readonly string $value
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return "{$this->name}=\"{$this->value}\"";
    }
}
