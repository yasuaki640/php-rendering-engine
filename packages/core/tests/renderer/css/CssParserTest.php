<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests\Renderer\Css;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssParser;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssToken;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssTokenizer;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssTokenType;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\Declaration;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\QualifiedRule;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\Selector;

class CssParserTest extends TestCase
{
    public function testEmpty(): void
    {
        $style = '';
        $tokenizer = new CssTokenizer($style);
        $parser = new CssParser($tokenizer);
        $stylesheet = $parser->parseStylesheet();

        $this->assertCount(0, $stylesheet->getRules());
    }

    public function testOneRule(): void
    {
        $style = 'p { color: red; }';
        $tokenizer = new CssTokenizer($style);
        $parser = new CssParser($tokenizer);
        $stylesheet = $parser->parseStylesheet();

        // 期待されるルールを作成
        $expectedRule = QualifiedRule::new();
        $expectedRule->setSelector(Selector::typeSelector('p'));
        $expectedDeclaration = Declaration::new();
        $expectedDeclaration->setProperty('color');
        $expectedDeclaration->setValue(new CssToken(CssTokenType::Ident, 'red'));
        $expectedRule->setDeclarations([$expectedDeclaration]);

        $this->assertCount(1, $stylesheet->getRules());
        $this->assertTrue($expectedRule->equals($stylesheet->getRules()[0]));
    }

    public function testIdSelector(): void
    {
        $style = '#id { color: red; }';
        $tokenizer = new CssTokenizer($style);
        $parser = new CssParser($tokenizer);
        $stylesheet = $parser->parseStylesheet();

        // 期待されるルールを作成
        $expectedRule = QualifiedRule::new();
        $expectedRule->setSelector(Selector::idSelector('id'));
        $expectedDeclaration = Declaration::new();
        $expectedDeclaration->setProperty('color');
        $expectedDeclaration->setValue(new CssToken(CssTokenType::Ident, 'red'));
        $expectedRule->setDeclarations([$expectedDeclaration]);

        $this->assertCount(1, $stylesheet->getRules());
        $this->assertTrue($expectedRule->equals($stylesheet->getRules()[0]));
    }

    public function testClassSelector(): void
    {
        $style = '.class { color: red; }';
        $tokenizer = new CssTokenizer($style);
        $parser = new CssParser($tokenizer);
        $stylesheet = $parser->parseStylesheet();

        // 期待されるルールを作成
        $expectedRule = QualifiedRule::new();
        $expectedRule->setSelector(Selector::classSelector('class'));
        $expectedDeclaration = Declaration::new();
        $expectedDeclaration->setProperty('color');
        $expectedDeclaration->setValue(new CssToken(CssTokenType::Ident, 'red'));
        $expectedRule->setDeclarations([$expectedDeclaration]);

        $this->assertCount(1, $stylesheet->getRules());
        $this->assertTrue($expectedRule->equals($stylesheet->getRules()[0]));
    }

    public function testMultipleRules(): void
    {
        $style = 'p { content: "Hey"; } h1 { font-size: 40; color: blue; }';
        $tokenizer = new CssTokenizer($style);
        $parser = new CssParser($tokenizer);
        $stylesheet = $parser->parseStylesheet();

        // 最初のルール: p { content: "Hey"; }
        $expectedRule1 = QualifiedRule::new();
        $expectedRule1->setSelector(Selector::typeSelector('p'));
        $expectedDeclaration1 = Declaration::new();
        $expectedDeclaration1->setProperty('content');
        $expectedDeclaration1->setValue(new CssToken(CssTokenType::StringToken, 'Hey'));
        $expectedRule1->setDeclarations([$expectedDeclaration1]);

        // 2番目のルール: h1 { font-size: 40; color: blue; }
        $expectedRule2 = QualifiedRule::new();
        $expectedRule2->setSelector(Selector::typeSelector('h1'));
        $expectedDeclaration2a = Declaration::new();
        $expectedDeclaration2a->setProperty('font-size');
        $expectedDeclaration2a->setValue(new CssToken(CssTokenType::Number, 40.0));
        $expectedDeclaration2b = Declaration::new();
        $expectedDeclaration2b->setProperty('color');
        $expectedDeclaration2b->setValue(new CssToken(CssTokenType::Ident, 'blue'));
        $expectedRule2->setDeclarations([$expectedDeclaration2a, $expectedDeclaration2b]);

        $this->assertCount(2, $stylesheet->getRules());
        $this->assertTrue($expectedRule1->equals($stylesheet->getRules()[0]));
        $this->assertTrue($expectedRule2->equals($stylesheet->getRules()[1]));
    }
}
