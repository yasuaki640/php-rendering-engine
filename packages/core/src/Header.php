<?php

declare(strict_types=1);

namespace MyApp\Core;

class Header
{
    public function __construct(
        public readonly string $name,
        public readonly string $value
    ) {
    }
}
