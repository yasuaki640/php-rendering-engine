<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

use MyApp\Core\Exception\UnexpectedInputException;

class Color
{
    private ?string $name;
    private string $code;

    public function __construct(?string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    public static function fromName(string $name): self
    {
        $code = match ($name) {
            'black' => '#000000',
            'silver' => '#c0c0c0',
            'gray' => '#808080',
            'white' => '#ffffff',
            'maroon' => '#800000',
            'red' => '#ff0000',
            'purple' => '#800080',
            'fuchsia' => '#ff00ff',
            'green' => '#008000',
            'lime' => '#00ff00',
            'olive' => '#808000',
            'yellow' => '#ffff00',
            'navy' => '#000080',
            'blue' => '#0000ff',
            'teal' => '#008080',
            'aqua' => '#00ffff',
            'orange' => '#ffa500',
            'lightgray' => '#d3d3d3',
            default => throw new UnexpectedInputException("color name '$name' is not supported yet"),
        };

        return new self($name, $code);
    }

    public static function fromCode(string $code): self
    {
        if (! str_starts_with($code, '#') || strlen($code) !== 7) {
            throw new UnexpectedInputException("invalid color code $code");
        }

        $name = match ($code) {
            '#000000' => 'black',
            '#c0c0c0' => 'silver',
            '#808080' => 'gray',
            '#ffffff' => 'white',
            '#800000' => 'maroon',
            '#ff0000' => 'red',
            '#800080' => 'purple',
            '#ff00ff' => 'fuchsia',
            '#008000' => 'green',
            '#00ff00' => 'lime',
            '#808000' => 'olive',
            '#ffff00' => 'yellow',
            '#000080' => 'navy',
            '#0000ff' => 'blue',
            '#008080' => 'teal',
            '#00ffff' => 'aqua',
            '#ffa500' => 'orange',
            '#d3d3d3' => 'lightgray',
            default => throw new UnexpectedInputException("color code '$code' is not supported yet"),
        };

        return new self($name, $code);
    }

    public static function white(): self
    {
        return new self('white', '#ffffff');
    }

    public static function black(): self
    {
        return new self('black', '#000000');
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCodeAsInt(): int
    {
        return hexdec(ltrim($this->code, '#'));
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function equals(Color $other): bool
    {
        return $this->code === $other->code;
    }
}
