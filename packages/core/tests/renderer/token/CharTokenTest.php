<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests\Token;

use MyApp\Core\Renderer\Token\CharToken;
use PHPUnit\Framework\TestCase;

class CharTokenTest extends TestCase
{
    public function testConstruct(): void
    {
        $token = new CharToken('a');

        $this->assertEquals('a', $token->char);
        $this->assertEquals('a', $token->getChar());
    }

    public function testGetType(): void
    {
        $token = new CharToken('x');

        $this->assertEquals('Char', $token->getType());
    }

    public function testWithSpecialCharacters(): void
    {
        $specialChars = [' ', '\n', '\t', '<', '>', '&', '"', "'"];

        foreach ($specialChars as $char) {
            $token = new CharToken($char);
            $this->assertEquals($char, $token->getChar());
            $this->assertEquals('Char', $token->getType());
        }
    }

    public function testWithEmptyString(): void
    {
        $token = new CharToken('');

        $this->assertEquals('', $token->getChar());
        $this->assertEquals('Char', $token->getType());
    }

    public function testWithMultibyteCharacter(): void
    {
        $token = new CharToken('あ');

        $this->assertEquals('あ', $token->getChar());
        $this->assertEquals('Char', $token->getType());
    }

    public function testWithNumber(): void
    {
        $token = new CharToken('5');

        $this->assertEquals('5', $token->getChar());
        $this->assertEquals('Char', $token->getType());
    }
}
