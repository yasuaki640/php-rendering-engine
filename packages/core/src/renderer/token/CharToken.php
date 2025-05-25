<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

class CharToken implements HtmlToken
{
    public function __construct(
        public readonly string $char
    ) {}

    public function getType(): string
    {
        return 'Char';
    }

    public function getChar(): string
    {
        return $this->char;
    }
}
