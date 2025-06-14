<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Tests\Token;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\EofToken;

class EofTokenTest extends TestCase
{
    public function testConstruct(): void
    {
        $token = new EofToken();

        $this->assertInstanceOf(EofToken::class, $token);
    }

    public function testGetType(): void
    {
        $token = new EofToken();

        $this->assertEquals('Eof', $token->getType());
    }

    public function testMultipleInstances(): void
    {
        $token1 = new EofToken();
        $token2 = new EofToken();

        $this->assertEquals($token1->getType(), $token2->getType());
        $this->assertNotSame($token1, $token2);
    }
}
