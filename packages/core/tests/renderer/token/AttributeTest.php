<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Tests\Token;

use MyApp\Core\Renderer\Html\Attribute;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testConstruct(): void
    {
        $attribute = new Attribute('id', 'test-value');

        $this->assertEquals('id', $attribute->name);
        $this->assertEquals('test-value', $attribute->value);
    }

    public function testConstructWithDefaults(): void
    {
        $attribute = new Attribute();

        $this->assertEquals('', $attribute->name);
        $this->assertEquals('', $attribute->value);
    }

    public function testConstructWithNameOnly(): void
    {
        $attribute = new Attribute('class');

        $this->assertEquals('class', $attribute->name);
        $this->assertEquals('', $attribute->value);
    }

    public function testNew(): void
    {
        $attribute = Attribute::new();

        $this->assertInstanceOf(Attribute::class, $attribute);
        $this->assertEquals('', $attribute->name);
        $this->assertEquals('', $attribute->value);
    }

    public function testAddCharToName(): void
    {
        $attribute = new Attribute();

        $newAttribute = $attribute->addChar('i', true);
        $this->assertEquals('i', $newAttribute->name);
        $this->assertEquals('', $newAttribute->value);

        $newAttribute2 = $newAttribute->addChar('d', true);
        $this->assertEquals('id', $newAttribute2->name);
        $this->assertEquals('', $newAttribute2->value);

        // 元のオブジェクトは変更されていないことを確認
        $this->assertEquals('', $attribute->name);
        $this->assertEquals('i', $newAttribute->name);
    }

    public function testAddCharToValue(): void
    {
        $attribute = new Attribute('id', '');

        $newAttribute = $attribute->addChar('t', false);
        $this->assertEquals('id', $newAttribute->name);
        $this->assertEquals('t', $newAttribute->value);

        $newAttribute2 = $newAttribute->addChar('e', false);
        $this->assertEquals('id', $newAttribute2->name);
        $this->assertEquals('te', $newAttribute2->value);

        $newAttribute3 = $newAttribute2->addChar('s', false);
        $newAttribute3 = $newAttribute3->addChar('t', false);
        $this->assertEquals('id', $newAttribute3->name);
        $this->assertEquals('test', $newAttribute3->value);

        // 元のオブジェクトは変更されていないことを確認
        $this->assertEquals('', $attribute->value);
    }

    public function testAddCharImmutability(): void
    {
        $attribute = new Attribute('class', 'initial');

        $newAttribute = $attribute->addChar('X', true);

        // 元のオブジェクトは変更されない
        $this->assertEquals('class', $attribute->name);
        $this->assertEquals('initial', $attribute->value);

        // 新しいオブジェクトが作成される
        $this->assertEquals('classX', $newAttribute->name);
        $this->assertEquals('initial', $newAttribute->value);
        $this->assertNotSame($attribute, $newAttribute);
    }

    public function testAddCharWithSpecialCharacters(): void
    {
        $attribute = new Attribute();

        // 名前に特殊文字を追加
        $attr1 = $attribute->addChar('-', true);
        $attr2 = $attr1->addChar('_', true);
        $attr3 = $attr2->addChar(':', true);

        $this->assertEquals('-_:', $attr3->name);

        // 値に特殊文字を追加
        $attr4 = $attr3->addChar(' ', false);
        $attr5 = $attr4->addChar('&', false);
        $attr6 = $attr5->addChar('"', false);

        $this->assertEquals(' &"', $attr6->value);
    }

    public function testAddCharWithMultibyteCharacters(): void
    {
        $attribute = new Attribute();

        $attr1 = $attribute->addChar('あ', true);
        $attr2 = $attr1->addChar('い', true);

        $this->assertEquals('あい', $attr2->name);

        $attr3 = $attr2->addChar('う', false);
        $attr4 = $attr3->addChar('え', false);

        $this->assertEquals('あい', $attr4->name);
        $this->assertEquals('うえ', $attr4->value);
    }

    public function testBuildingCompleteAttribute(): void
    {
        $attribute = Attribute::new();

        // 名前を構築
        $chars = ['c', 'l', 'a', 's', 's'];
        foreach ($chars as $char) {
            $attribute = $attribute->addChar($char, true);
        }

        $this->assertEquals('class', $attribute->name);
        $this->assertEquals('', $attribute->value);

        // 値を構築
        $valueChars = ['m', 'y', '-', 'c', 'l', 'a', 's', 's'];
        foreach ($valueChars as $char) {
            $attribute = $attribute->addChar($char, false);
        }

        $this->assertEquals('class', $attribute->name);
        $this->assertEquals('my-class', $attribute->value);
    }

    public function testChainedAddChar(): void
    {
        $attribute = Attribute::new()
            ->addChar('i', true)
            ->addChar('d', true)
            ->addChar('t', false)
            ->addChar('e', false)
            ->addChar('s', false)
            ->addChar('t', false);

        $this->assertEquals('id', $attribute->name);
        $this->assertEquals('test', $attribute->value);
    }

    public function testEmptyStringAddition(): void
    {
        $attribute = new Attribute('name', 'value');

        // 空文字を追加してもそのまま
        $newAttribute = $attribute->addChar('', true);
        $this->assertEquals('name', $newAttribute->name);
        $this->assertEquals('value', $newAttribute->value);

        $newAttribute2 = $attribute->addChar('', false);
        $this->assertEquals('name', $newAttribute2->name);
        $this->assertEquals('value', $newAttribute2->value);
    }

    public function testReadonlyProperties(): void
    {
        $attribute = new Attribute('test-name', 'test-value');

        // readonlyプロパティは直接変更できない
        $this->assertEquals('test-name', $attribute->name);
        $this->assertEquals('test-value', $attribute->value);

        // addCharメソッドで新しいインスタンスが作成される
        $newAttribute = $attribute->addChar('X', true);
        $this->assertEquals('test-nameX', $newAttribute->name);
        $this->assertEquals('test-value', $newAttribute->value);

        // 元のオブジェクトは変更されない
        $this->assertEquals('test-name', $attribute->name);
        $this->assertEquals('test-value', $attribute->value);
    }
}
