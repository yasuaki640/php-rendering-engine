<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Token;

interface HtmlToken
{
    public function getType(): string;
}
