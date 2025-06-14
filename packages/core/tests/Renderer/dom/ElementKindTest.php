<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Dom;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\ElementKind;

class ElementKindTest extends TestCase
{
    public function testFromStringValidElements(): void
    {
        $validElements = [
            'html' => ElementKind::Html,
            'head' => ElementKind::Head,
            'style' => ElementKind::Style,
            'script' => ElementKind::Script,
            'body' => ElementKind::Body,
            'p' => ElementKind::P,
            'h1' => ElementKind::H1,
            'h2' => ElementKind::H2,
            'a' => ElementKind::A,
            'div' => ElementKind::Div,
        ];

        foreach ($validElements as $tagName => $expectedKind) {
            $actualKind = ElementKind::fromString($tagName);
            $this->assertEquals($expectedKind, $actualKind);
            $this->assertEquals($tagName, $actualKind->value);
        }
    }

    public function testFromStringInvalidElement(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unimplemented element name: unknown');

        ElementKind::fromString('unknown');
    }

    public function testElementKindValues(): void
    {
        $this->assertEquals('html', ElementKind::Html->value);
        $this->assertEquals('head', ElementKind::Head->value);
        $this->assertEquals('style', ElementKind::Style->value);
        $this->assertEquals('script', ElementKind::Script->value);
        $this->assertEquals('body', ElementKind::Body->value);
        $this->assertEquals('p', ElementKind::P->value);
        $this->assertEquals('h1', ElementKind::H1->value);
        $this->assertEquals('h2', ElementKind::H2->value);
        $this->assertEquals('a', ElementKind::A->value);
        $this->assertEquals('div', ElementKind::Div->value);
    }

    public function testAllElementKinds(): void
    {
        $allKinds = ElementKind::cases();

        $this->assertCount(23, $allKinds);
        $this->assertContains(ElementKind::Html, $allKinds);
        $this->assertContains(ElementKind::Head, $allKinds);
        $this->assertContains(ElementKind::Style, $allKinds);
        $this->assertContains(ElementKind::Script, $allKinds);
        $this->assertContains(ElementKind::Body, $allKinds);
        $this->assertContains(ElementKind::P, $allKinds);
        $this->assertContains(ElementKind::H1, $allKinds);
        $this->assertContains(ElementKind::H2, $allKinds);
        $this->assertContains(ElementKind::A, $allKinds);
        $this->assertContains(ElementKind::Div, $allKinds);
    }
}
