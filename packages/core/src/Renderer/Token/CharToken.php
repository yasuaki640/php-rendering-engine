<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Token;

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
