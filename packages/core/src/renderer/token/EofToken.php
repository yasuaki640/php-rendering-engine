<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

class EofToken implements HtmlToken
{
    public function getType(): string
    {
        return 'Eof';
    }
}
