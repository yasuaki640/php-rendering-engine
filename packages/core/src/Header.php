<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core;

class Header
{
    public function __construct(
        public readonly string $name,
        public readonly string $value
    ) {}
}
