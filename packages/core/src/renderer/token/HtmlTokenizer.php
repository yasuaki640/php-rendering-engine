<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

use MyApp\Core\Renderer\Html\Attribute;

class HtmlTokenizer
{
    private string $input;
    private int $pos = 0;
    private bool $reconsume = false;
    private State $state;
    private ?HtmlToken $currentTag = null;
    private string $buf = '';

    public function __construct(string $input)
    {
        $this->input = $input;
        $this->state = State::Data;
    }


    public function next(): ?HtmlToken
    {
        if ($this->pos >= strlen($this->input)) {
            return null;
        }

        while (true) {
            $c = $this->reconsume ? $this->reconsumeInput() : $this->consumeNextInput();

            switch ($this->state) {
                case State::Data:
                    if ($c === '<') {
                        $this->state = State::TagOpen;

                        continue 2;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    return HtmlTokenFactory::createChar($c);
                case State::TagOpen:
                    if ($c === '/') {
                        $this->state = State::EndTagOpen;

                        continue 2;
                    }

                    if (ctype_alpha($c)) {
                        $this->reconsume = true;
                        $this->state = State::TagName;
                        $this->createTag(true);

                        continue 2;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->reconsume = true;
                    $this->state = State::Data;

                    continue 2;
                case State::EndTagOpen:
                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    if (ctype_alpha($c)) {
                        $this->reconsume = true;
                        $this->state = State::TagName;
                        $this->createTag(false);

                        continue 2;
                    }

                    // 無効な文字の場合、次の文字の処理に進む
                    break;
                case State::TagName:
                    if ($c === ' ') {
                        $this->state = State::BeforeAttributeName;

                        continue 2;
                    }

                    if ($c === '/') {
                        $this->state = State::SelfClosingStartTag;

                        continue 2;
                    }

                    if ($c === '>') {
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if (ctype_upper($c)) {
                        $this->appendTagName(strtolower($c));

                        continue 2;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->appendTagName($c);

                    continue 2;
                case State::BeforeAttributeName:
                    if ($c === '/' || $c === '>' || $this->isEof()) {
                        $this->reconsume = true;
                        $this->state = State::AfterAttributeName;

                        continue 2;
                    }

                    $this->reconsume = true;
                    $this->state = State::AttributeName;
                    $this->startNewAttribute();

                    continue 2;
                case State::AttributeName:
                    if ($c === ' ' || $c === '/' || $c === '>' || $this->isEof()) {
                        $this->reconsume = true;
                        $this->state = State::AfterAttributeName;

                        continue 2;
                    }

                    if ($c === '=') {
                        $this->state = State::BeforeAttributeValue;

                        continue 2;
                    }

                    if (ctype_upper($c)) {
                        $this->appendAttribute(strtolower($c), true);

                        continue 2;
                    }

                    $this->appendAttribute($c, true);

                    continue 2;
                case State::AfterAttributeName:
                    if ($c === ' ') {
                        // 空白文字は無視する
                        continue 2;
                    }

                    if ($c === '/') {
                        $this->state = State::SelfClosingStartTag;

                        continue 2;
                    }

                    if ($c === '=') {
                        $this->state = State::BeforeAttributeValue;

                        continue 2;
                    }

                    if ($c === '>') {
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->reconsume = true;
                    $this->state = State::BeforeAttributeName;

                    continue 2;
                case State::BeforeAttributeValue:
                    if ($c === ' ') {
                        // 空白文字は無視する
                        continue 2;
                    }

                    if ($c === '"') {
                        $this->state = State::AttributeValueDoubleQuoted;

                        continue 2;
                    }

                    if ($c === "'") {
                        $this->state = State::AttributeValueSingleQuoted;

                        continue 2;
                    }

                    $this->reconsume = true;
                    $this->state = State::AttributeValueUnquoted;

                    continue 2;
                case State::AttributeValueDoubleQuoted:
                    if ($c === '"') {
                        $this->state = State::AfterAttributeValueQuoted;

                        continue 2;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->appendAttribute($c, false);

                    continue 2;
                case State::AttributeValueSingleQuoted:
                    if ($c === "'") {
                        $this->state = State::AfterAttributeValueQuoted;

                        continue 2;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->appendAttribute($c, false);

                    continue 2;
                case State::AttributeValueUnquoted:
                    if ($c === ' ') {
                        $this->state = State::BeforeAttributeName;

                        continue 2;
                    }

                    if ($c === '>') {
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->appendAttribute($c, false);

                    continue 2;
                case State::AfterAttributeValueQuoted:
                    if ($c === ' ') {
                        $this->state = State::BeforeAttributeName;

                        continue 2;
                    }

                    if ($c === '/') {
                        $this->state = State::SelfClosingStartTag;

                        continue 2;
                    }

                    if ($c === '>') {
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->reconsume = true;
                    $this->state = State::BeforeAttributeName;

                    continue 2;
                case State::SelfClosingStartTag:
                    if ($c === '>') {
                        $this->setSelfClosingFlag();
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if ($this->isEof()) {
                        // invalid parse error.
                        return HtmlTokenFactory::createEof();
                    }

                    continue 2;
                case State::ScriptData:
                    if ($c === '<') {
                        $this->state = State::ScriptDataLessThanSign;

                        continue 2;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    return HtmlTokenFactory::createChar($c);
                case State::ScriptDataLessThanSign:
                    if ($c === '/') {
                        // 一時的なバッファを空文字でリセットする
                        $this->buf = '';
                        $this->state = State::ScriptDataEndTagOpen;

                        continue 2;
                    }

                    $this->reconsume = true;
                    $this->state = State::ScriptData;

                    return HtmlTokenFactory::createChar('<');
                case State::ScriptDataEndTagOpen:
                    if (ctype_alpha($c)) {
                        $this->reconsume = true;
                        $this->state = State::ScriptDataEndTagName;
                        $this->createTag(false);

                        continue 2;
                    }

                    $this->reconsume = true;
                    $this->state = State::ScriptData;

                    // 仕様では、"<"と"/"の2つの文字トークンを返すとなっているが、
                    // 私たちの実装ではnextメソッドからは一つのトークンしか返せないため、"<"のトークンのみを返す
                    return HtmlTokenFactory::createChar('<');
                case State::ScriptDataEndTagName:
                    if ($c === '>') {
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if (ctype_alpha($c)) {
                        $this->buf .= $c;
                        $this->appendTagName(strtolower($c));

                        continue 2;
                    }

                    $this->state = State::TemporaryBuffer;
                    $this->buf = '</' . $this->buf;
                    $this->buf .= $c;

                    continue 2;
                case State::TemporaryBuffer:
                    $this->reconsume = true;

                    if (strlen($this->buf) === 0) {
                        $this->state = State::ScriptData;

                        continue 2;
                    }

                    // remove the first char
                    $c = $this->buf[0];
                    $this->buf = substr($this->buf, 1);

                    return HtmlTokenFactory::createChar($c);
                default:
                    throw new \InvalidArgumentException('Unknown state');
            }
        }
    }

    private function isEof(): bool
    {
        return $this->pos >= strlen($this->input);
    }

    private function consumeNextInput(): string
    {
        if ($this->pos >= strlen($this->input)) {
            return '';
        }

        return $this->input[$this->pos++];
    }

    private function reconsumeInput(): string
    {
        $this->reconsume = false;

        return $this->input[$this->pos - 1];
    }

    private function createTag(bool $startTag): void
    {
        if ($startTag) {
            $this->currentTag = HtmlTokenFactory::createStartTag('', false, []);
        } else {
            $this->currentTag = HtmlTokenFactory::createEndTag('');
        }
    }

    private function appendTagName(string $c): void
    {
        if ($this->currentTag === null) {
            throw new \RuntimeException('currentTag should not be null');
        }

        if ($this->currentTag instanceof StartTag) {
            $this->currentTag = HtmlTokenFactory::createStartTag(
                $this->currentTag->getTag() . $c,
                $this->currentTag->isSelfClosing(),
                $this->currentTag->getAttributes()
            );
        } elseif ($this->currentTag instanceof EndTag) {
            $this->currentTag = HtmlTokenFactory::createEndTag(
                $this->currentTag->getTag() . $c
            );
        } else {
            throw new \RuntimeException('currentTag should be either StartTag or EndTag');
        }
    }

    private function takeLatestToken(): ?HtmlToken
    {
        if ($this->currentTag === null) {
            throw new \RuntimeException('currentTag should not be null');
        }

        $token = $this->currentTag;
        $this->currentTag = null;

        if ($this->currentTag !== null) {
            throw new \RuntimeException('currentTag should be null after taking latest token');
        }

        return $token;
    }

    private function startNewAttribute(): void
    {
        if ($this->currentTag === null) {
            throw new \RuntimeException('currentTag should not be null');
        }

        if (! ($this->currentTag instanceof StartTag)) {
            throw new \RuntimeException('currentTag should be StartTag');
        }

        $currentAttributes = $this->currentTag->getAttributes();
        $currentAttributes[] = new Attribute();

        $this->currentTag = HtmlTokenFactory::createStartTag(
            $this->currentTag->getTag(),
            $this->currentTag->isSelfClosing(),
            $currentAttributes
        );
    }

    private function appendAttribute(string $c, bool $isName): void
    {
        if ($this->currentTag === null) {
            throw new \RuntimeException('currentTag should not be null');
        }

        if (! ($this->currentTag instanceof StartTag)) {
            throw new \RuntimeException('currentTag should be StartTag');
        }

        $attributes = $this->currentTag->getAttributes();
        $len = count($attributes);

        if ($len === 0) {
            throw new \RuntimeException('attributes array should not be empty');
        }

        $attributes[$len - 1] = $attributes[$len - 1]->addChar($c, $isName);

        $this->currentTag = HtmlTokenFactory::createStartTag(
            $this->currentTag->getTag(),
            $this->currentTag->isSelfClosing(),
            $attributes
        );
    }

    private function setSelfClosingFlag(): void
    {
        if ($this->currentTag === null) {
            throw new \RuntimeException('currentTag should not be null');
        }

        if (! ($this->currentTag instanceof StartTag)) {
            throw new \RuntimeException('currentTag should be StartTag');
        }

        $this->currentTag = HtmlTokenFactory::createStartTag(
            $this->currentTag->getTag(),
            true,
            $this->currentTag->getAttributes()
        );
    }
}
