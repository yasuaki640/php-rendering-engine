<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Token;

class EndTag implements HtmlToken
{
    public function __construct(
        public readonly string $tag
    ) {}

    public function getType(): string
    {
        return 'EndTag';
    }

    public function getTag(): string
    {
        return $this->tag;
    }
}
