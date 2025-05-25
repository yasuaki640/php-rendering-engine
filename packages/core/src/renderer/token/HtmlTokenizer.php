<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Token;

use MyApp\Core\Renderer\Attribute;

class HtmlTokenizer
{
    private string $input;
    private int $pos = 0;
    private bool $reconsume = false;
    private State $state;
    private ?HtmlToken $currentTag = null;

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

                        continue;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    return HtmlTokenFactory::createChar($c);
                case State::TagOpen:
                    if ($c === '/') {
                        $this->state = State::EndTagOpen;

                        continue;
                    }

                    if (ctype_alpha($c)) {
                        $this->reconsume = true;
                        $this->state = State::TagName;
                        $this->createTag(true);

                        continue;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->reconsume = true;
                    $this->state = State::Data;

                    continue;
                case State::EndTagOpen:
                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    if (ctype_alpha($c)) {
                        $this->reconsume = true;
                        $this->state = State::TagName;
                        $this->createTag(false);

                        continue;
                    }

                    break;
                case State::TagName:
                    if ($c === ' ') {
                        $this->state = State::BeforeAttributeName;

                        continue;
                    }

                    if ($c === '/') {
                        $this->state = State::SelfClosingStartTag;

                        continue;
                    }

                    if ($c === '>') {
                        $this->state = State::Data;

                        return $this->takeLatestToken();
                    }

                    if (ctype_upper($c)) {
                        $this->appendTagName(strtolower($c));

                        continue;
                    }

                    if ($this->isEof()) {
                        return HtmlTokenFactory::createEof();
                    }

                    $this->appendTagName($c);

                    continue;
                case State::BeforeAttributeName:
                    if ($c === '/' || $c === '>' || $this->isEof()) {
                        $this->reconsume = true;
                        $this->state = State::AfterAttributeName;

                        continue;
                    }

                    $this->reconsume = true;
                    $this->state = State::AttributeName;
                    $this->startNewAttribute();

                    continue;
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

        if (!($this->currentTag instanceof StartTag)) {
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
}
