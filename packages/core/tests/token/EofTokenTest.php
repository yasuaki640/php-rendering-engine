<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Token;

use MyApp\Core\Renderer\Token\EofToken;
use PHPUnit\Framework\TestCase;

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
