<?php

declare(strict_types=1);

namespace MyApp\Core\Tests\Token;

use MyApp\Core\Renderer\Token\State;
use PHPUnit\Framework\TestCase;

class StateTest extends TestCase
{
    public function testAllStatesCases(): void
    {
        $expectedStates = [
            'Data',
            'TagOpen',
            'EndTagOpen',
            'TagName',
            'BeforeAttributeName',
            'AttributeName',
            'AfterAttributeName',
            'BeforeAttributeValue',
            'AttributeValueDoubleQuoted',
            'AttributeValueSingleQuoted',
            'AttributeValueUnquoted',
            'AfterAttributeValueQuoted',
            'SelfClosingStartTag',
            'ScriptData',
            'ScriptDataLessThanSign',
            'ScriptDataEndTagOpen',
            'ScriptDataEndTagName',
            'TemporaryBuffer',
        ];

        $actualStates = [];
        foreach (State::cases() as $state) {
            $actualStates[] = $state->name;
        }

        $this->assertEquals($expectedStates, $actualStates);
        $this->assertCount(18, State::cases());
    }

    public function testDataState(): void
    {
        $state = State::Data;
        $this->assertEquals('Data', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testTagOpenState(): void
    {
        $state = State::TagOpen;
        $this->assertEquals('TagOpen', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testEndTagOpenState(): void
    {
        $state = State::EndTagOpen;
        $this->assertEquals('EndTagOpen', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testTagNameState(): void
    {
        $state = State::TagName;
        $this->assertEquals('TagName', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testBeforeAttributeNameState(): void
    {
        $state = State::BeforeAttributeName;
        $this->assertEquals('BeforeAttributeName', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testAttributeNameState(): void
    {
        $state = State::AttributeName;
        $this->assertEquals('AttributeName', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testAfterAttributeNameState(): void
    {
        $state = State::AfterAttributeName;
        $this->assertEquals('AfterAttributeName', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testBeforeAttributeValueState(): void
    {
        $state = State::BeforeAttributeValue;
        $this->assertEquals('BeforeAttributeValue', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testAttributeValueDoubleQuotedState(): void
    {
        $state = State::AttributeValueDoubleQuoted;
        $this->assertEquals('AttributeValueDoubleQuoted', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testAttributeValueSingleQuotedState(): void
    {
        $state = State::AttributeValueSingleQuoted;
        $this->assertEquals('AttributeValueSingleQuoted', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testAttributeValueUnquotedState(): void
    {
        $state = State::AttributeValueUnquoted;
        $this->assertEquals('AttributeValueUnquoted', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testAfterAttributeValueQuotedState(): void
    {
        $state = State::AfterAttributeValueQuoted;
        $this->assertEquals('AfterAttributeValueQuoted', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testSelfClosingStartTagState(): void
    {
        $state = State::SelfClosingStartTag;
        $this->assertEquals('SelfClosingStartTag', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testScriptDataState(): void
    {
        $state = State::ScriptData;
        $this->assertEquals('ScriptData', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testScriptDataLessThanSignState(): void
    {
        $state = State::ScriptDataLessThanSign;
        $this->assertEquals('ScriptDataLessThanSign', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testScriptDataEndTagOpenState(): void
    {
        $state = State::ScriptDataEndTagOpen;
        $this->assertEquals('ScriptDataEndTagOpen', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testScriptDataEndTagNameState(): void
    {
        $state = State::ScriptDataEndTagName;
        $this->assertEquals('ScriptDataEndTagName', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testTemporaryBufferState(): void
    {
        $state = State::TemporaryBuffer;
        $this->assertEquals('TemporaryBuffer', $state->name);
        $this->assertInstanceOf(State::class, $state);
    }

    public function testStateComparison(): void
    {
        $this->assertSame(State::Data, State::Data);
        $this->assertNotSame(State::Data, State::TagOpen);

        $this->assertTrue(State::Data === State::Data);
        $this->assertFalse(State::Data === State::TagOpen);
    }

    public function testStateInArray(): void
    {
        $states = [State::Data, State::TagOpen, State::TagName];

        $this->assertContains(State::Data, $states);
        $this->assertContains(State::TagOpen, $states);
        $this->assertContains(State::TagName, $states);
        $this->assertNotContains(State::EndTagOpen, $states);
    }
}
