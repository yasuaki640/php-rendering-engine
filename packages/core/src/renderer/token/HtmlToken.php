<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

interface HtmlToken
{
    public function getType(): string;
}
