<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Token;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\EndTag;

class EndTagTest extends TestCase
{
    public function testConstruct(): void
    {
        $tag = new EndTag('div');

        $this->assertEquals('div', $tag->tag);
        $this->assertEquals('div', $tag->getTag());
    }

    public function testGetType(): void
    {
        $tag = new EndTag('p');

        $this->assertEquals('EndTag', $tag->getType());
    }

    public function testWithDifferentTagNames(): void
    {
        $tagNames = ['html', 'head', 'body', 'div', 'span', 'h1', 'h2', 'p', 'a', 'img'];

        foreach ($tagNames as $tagName) {
            $tag = new EndTag($tagName);
            $this->assertEquals($tagName, $tag->getTag());
            $this->assertEquals('EndTag', $tag->getType());
        }
    }

    public function testWithEmptyTagName(): void
    {
        $tag = new EndTag('');

        $this->assertEquals('', $tag->getTag());
        $this->assertEquals('EndTag', $tag->getType());
    }

    public function testWithUppercaseTagName(): void
    {
        $tag = new EndTag('DIV');

        $this->assertEquals('DIV', $tag->getTag());
        $this->assertEquals('EndTag', $tag->getType());
    }

    public function testWithSpecialCharactersInTagName(): void
    {
        $tag = new EndTag('custom-tag');

        $this->assertEquals('custom-tag', $tag->getTag());
        $this->assertEquals('EndTag', $tag->getType());
    }
}
