<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Token;

class EofToken implements HtmlToken
{
    public function getType(): string
    {
        return 'Eof';
    }
}
