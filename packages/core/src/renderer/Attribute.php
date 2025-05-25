<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer;

class Attribute
{
    public readonly string $name;
    public readonly string $value;

    public function __construct(string $name = '', string $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function new(): self
    {
        return new self();
    }

    public function addChar(string $char, bool $isName): self
    {
        if ($isName) {
            return new self($this->name . $char, $this->value);
        } else {
            return new self($this->name, $this->value . $char);
        }
    }
}
