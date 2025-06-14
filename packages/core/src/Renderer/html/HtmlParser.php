<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Html;

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Window;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\CharToken;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\EndTag;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\EofToken;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\HtmlToken;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\HtmlTokenizer;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\StartTag;

/**
 * HTMLパーサー - HTML文書をトークン化してDOMツリーを構築
 *
 * HTML Standard仕様のtree constructionアルゴリズムに基づく実装
 * @see https://html.spec.whatwg.org/multipage/parsing.html#tree-construction
 */
class HtmlParser
{
    private Window $window;
    private InsertionMode $mode;
    private InsertionMode $originalInsertionMode;
    /** @var Node[] */
    private array $stackOfOpenElements;
    private HtmlTokenizer $tokenizer;

    public function __construct(string $html)
    {
        $this->window = new Window();
        $this->mode = InsertionMode::Initial;
        $this->originalInsertionMode = InsertionMode::Initial;
        $this->stackOfOpenElements = [];
        $this->tokenizer = new HtmlTokenizer($html);
    }

    /**
     * HTMLをパースしてDOMツリーを構築
     */
    public function constructTree(): Window
    {
        while (true) {
            $token = $this->tokenizer->next();

            if ($token === null) {
                break;
            }

            match ($this->mode) {
                InsertionMode::Initial => $this->handleInitialMode($token),
                InsertionMode::BeforeHtml => $this->handleBeforeHtmlMode($token),
                InsertionMode::BeforeHead => $this->handleBeforeHeadMode($token),
                InsertionMode::InHead => $this->handleInHeadMode($token),
                InsertionMode::AfterHead => $this->handleAfterHeadMode($token),
                InsertionMode::InBody => $this->handleInBodyMode($token),
                InsertionMode::Text => $this->handleTextMode($token),
                InsertionMode::AfterBody => $this->handleAfterBodyMode($token),
                InsertionMode::AfterAfterBody => $this->handleAfterAfterBodyMode($token),
            };

            if ($token instanceof EofToken) {
                break;
            }
        }

        return $this->window;
    }

    /**
     * 初期モードの処理
     */
    private function handleInitialMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof CharToken:
                // 空白文字は無視
                if (trim($token->getChar()) === '') {
                    return;
                }
                // 非空白文字の場合は BeforeHtml モードに移行
                $this->mode = InsertionMode::BeforeHtml;
                $this->handleBeforeHtmlMode($token);

                break;

            case $token instanceof StartTag:
                $this->mode = InsertionMode::BeforeHtml;
                $this->handleBeforeHtmlMode($token);

                break;

            case $token instanceof EndTag:
                $this->mode = InsertionMode::BeforeHtml;
                $this->handleBeforeHtmlMode($token);

                break;

            case $token instanceof EofToken:
                $this->mode = InsertionMode::BeforeHtml;
                $this->handleBeforeHtmlMode($token);

                break;

            default:
                $this->mode = InsertionMode::BeforeHtml;
                $this->handleBeforeHtmlMode($token);

                break;
        }
    }

    /**
     * before html モードの処理
     */
    private function handleBeforeHtmlMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof StartTag && $token->getTag() === 'html':
                $htmlElement = $this->createElement($token);
                $this->window->getDocument()->appendChild($htmlElement);
                $this->stackOfOpenElements[] = $htmlElement;
                $this->mode = InsertionMode::BeforeHead;

                break;

            case $token instanceof CharToken:
                // 空白文字は無視
                if (trim($token->getChar()) === '') {
                    return;
                }
                // 暗黙的に html 要素を作成
                $this->insertImplicitHtmlElement();
                $this->mode = InsertionMode::BeforeHead;
                $this->handleBeforeHeadMode($token);

                break;

            case $token instanceof EndTag:
                if (! in_array($token->getTag(), ['head', 'body', 'html', 'br'])) {
                    return; // パースエラー - 無視
                }
                $this->insertImplicitHtmlElement();
                $this->mode = InsertionMode::BeforeHead;
                $this->handleBeforeHeadMode($token);

                break;

            default:
                $this->insertImplicitHtmlElement();
                $this->mode = InsertionMode::BeforeHead;
                $this->handleBeforeHeadMode($token);

                break;
        }
    }

    /**
     * before head モードの処理
     */
    private function handleBeforeHeadMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof StartTag && $token->getTag() === 'head':
                $headElement = $this->createElement($token);
                $this->insertElement($headElement);
                $this->mode = InsertionMode::InHead;

                break;

            case $token instanceof CharToken:
                // 空白文字は無視
                if (trim($token->getChar()) === '') {
                    return;
                }
                // 暗黙的に head 要素を作成
                $this->insertImplicitHeadElement();
                $this->mode = InsertionMode::InHead;
                $this->handleInHeadMode($token);

                break;

            case $token instanceof EndTag:
                if (! in_array($token->getTag(), ['head', 'body', 'html', 'br'])) {
                    return; // パースエラー - 無視
                }
                $this->insertImplicitHeadElement();
                $this->mode = InsertionMode::InHead;
                $this->handleInHeadMode($token);

                break;

            default:
                $this->insertImplicitHeadElement();
                $this->mode = InsertionMode::InHead;
                $this->handleInHeadMode($token);

                break;
        }
    }

    /**
     * in head モードの処理
     */
    private function handleInHeadMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof CharToken:
                // 空白文字は挿入
                if (trim($token->getChar()) === '') {
                    $this->insertChar($token->getChar());

                    return;
                }
                // 非空白文字の場合は head を終了
                $this->popCurrentNode();
                $this->mode = InsertionMode::AfterHead;
                $this->handleAfterHeadMode($token);

                break;

            case $token instanceof StartTag:
                $tagName = $token->getTag();
                if (in_array($tagName, ['title', 'meta', 'link', 'style', 'script'])) {
                    $element = $this->createElement($token);
                    $this->insertElement($element);

                    if (in_array($tagName, ['script', 'style'])) {
                        $this->originalInsertionMode = $this->mode;
                        $this->mode = InsertionMode::Text;
                    }

                    if (! $token->isSelfClosing() && ! in_array($tagName, ['script', 'style'])) {
                        $this->popCurrentNode();
                    }
                } else {
                    // その他の開始タグは head を終了
                    $this->popCurrentNode();
                    $this->mode = InsertionMode::AfterHead;
                    $this->handleAfterHeadMode($token);
                }

                break;

            case $token instanceof EndTag:
                if ($token->getTag() === 'head') {
                    $this->popCurrentNode();
                    $this->mode = InsertionMode::AfterHead;
                } else {
                    // 他の終了タグは head を終了
                    $this->popCurrentNode();
                    $this->mode = InsertionMode::AfterHead;
                    $this->handleAfterHeadMode($token);
                }

                break;

            default:
                $this->popCurrentNode();
                $this->mode = InsertionMode::AfterHead;
                $this->handleAfterHeadMode($token);

                break;
        }
    }

    /**
     * after head モードの処理
     */
    private function handleAfterHeadMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof StartTag && $token->getTag() === 'body':
                $bodyElement = $this->createElement($token);
                $this->insertElement($bodyElement);
                $this->mode = InsertionMode::InBody;

                break;

            case $token instanceof CharToken:
                // 空白文字は無視
                if (trim($token->getChar()) === '') {
                    return;
                }
                // 暗黙的に body 要素を作成
                $this->insertImplicitBodyElement();
                $this->mode = InsertionMode::InBody;
                $this->handleInBodyMode($token);

                break;

            default:
                $this->insertImplicitBodyElement();
                $this->mode = InsertionMode::InBody;
                $this->handleInBodyMode($token);

                break;
        }
    }

    /**
     * in body モードの処理
     */
    private function handleInBodyMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof CharToken:
                $this->insertChar($token->getChar());

                break;

            case $token instanceof StartTag:
                $tagName = $token->getTag();

                // p要素の特別な処理
                if ($tagName === 'p') {
                    $element = $this->createElement($token);
                    $this->insertElement($element);
                } elseif (in_array($tagName, ['h1', 'h2'])) {
                    // h1, h2要素の処理
                    $element = $this->createElement($token);
                    $this->insertElement($element);
                } elseif ($tagName === 'a') {
                    // a要素の処理
                    $element = $this->createElement($token);
                    $this->insertElement($element);
                } elseif (in_array($tagName, ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'])) {
                    // void要素（自己終了タグ）のチェック
                    $element = $this->createElement($token);
                    $this->insertElement($element);
                    $this->popCurrentNode(); // void要素はすぐにポップ
                } else {
                    $element = $this->createElement($token);
                    $this->insertElement($element);

                    // 自己終了タグの場合はポップ
                    if ($token->isSelfClosing()) {
                        $this->popCurrentNode();
                    }
                }

                break;

            case $token instanceof EndTag:
                $tagName = $token->getTag();

                if ($tagName === 'body') {
                    if ($this->containInStack('body')) {
                        $this->mode = InsertionMode::AfterBody;
                    }
                } elseif ($tagName === 'html') {
                    if ($this->containInStack('body')) {
                        $this->mode = InsertionMode::AfterBody;
                        $this->handleAfterBodyMode($token);
                    }
                } elseif ($tagName === 'p') {
                    // p要素の終了タグの処理
                    $this->popUntil($tagName);
                } elseif (in_array($tagName, ['h1', 'h2'])) {
                    // h1, h2要素の終了タグの処理
                    $this->popUntil($tagName);
                } elseif ($tagName === 'a') {
                    // a要素の終了タグの処理
                    $this->popUntil($tagName);
                } else {
                    // 対応する開始タグまでポップ
                    $this->popUntil($tagName);
                }

                break;

            case $token instanceof EofToken:
                // パースエラー：開いている要素がある場合
                if (! empty($this->stackOfOpenElements)) {
                    // ログまたはエラーハンドリング
                }

                break;

            default:
                break;
        }
    }

    /**
     * text モードの処理（script要素内など）
     */
    private function handleTextMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof CharToken:
                $this->insertChar($token->getChar());

                break;

            case $token instanceof EndTag:
                $tagName = $token->getTag();
                if (in_array($tagName, ['script', 'style'])) {
                    $this->popCurrentNode();
                    $this->mode = $this->originalInsertionMode;
                }

                break;

            case $token instanceof EofToken:
                $this->popCurrentNode();
                $this->mode = $this->originalInsertionMode;

                break;

            default:
                break;
        }
    }

    /**
     * after body モードの処理
     */
    private function handleAfterBodyMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof CharToken:
                // 空白文字のみ許可
                if (trim($token->getChar()) === '') {
                    $this->insertChar($token->getChar());

                    return;
                }
                // 非空白文字の場合はInBodyモードに戻る
                $this->mode = InsertionMode::InBody;
                $this->handleInBodyMode($token);

                break;

            case $token instanceof EndTag && $token->getTag() === 'html':
                $this->popUntil('html');
                $this->mode = InsertionMode::AfterAfterBody;

                break;

            case $token instanceof EofToken:
                // 正常終了
                break;

            default:
                // その他のトークンはInBodyモードで処理
                $this->mode = InsertionMode::InBody;
                $this->handleInBodyMode($token);

                break;
        }
    }

    /**
     * after after body モードの処理
     */
    private function handleAfterAfterBodyMode(HtmlToken $token): void
    {
        switch (true) {
            case $token instanceof CharToken:
                // 空白文字は無視
                if (trim($token->getChar()) === '') {
                    return;
                }

                break;

            case $token instanceof EofToken:
                break;

            default:
                // パースエラー
                break;
        }
    }

    /**
     * 要素をDOMツリーに挿入
     */
    private function insertElement(Node $element): void
    {
        $currentNode = $this->getCurrentNode();
        if ($currentNode) {
            $currentNode->appendChild($element);
        } else {
            $this->window->getDocument()->appendChild($element);
        }
        $this->stackOfOpenElements[] = $element;
    }

    /**
     * 文字をDOMツリーに挿入
     */
    private function insertChar(string $char): void
    {
        $currentNode = $this->getCurrentNode();
        if ($currentNode === null) {
            $currentNode = $this->window->getDocument();
        }

        // 最後の子がテキストノードの場合は結合
        $lastChild = $currentNode->getLastChild();
        if ($lastChild && $lastChild->getKind() === NodeKind::Text) {
            $currentText = $lastChild->getTextContent() ?? '';
            $lastChild->setTextContent($currentText . $char);

            return;
        }

        // 改行文字や空白文字の場合、新しいテキストノードは作成しない
        // ただし、既存のテキストノードがある場合は上記で文字を追加済み
        if ($char === "\n" || $char === ' ') {
            return;
        }

        // 新しいテキストノードを作成
        $textNode = new Node(NodeKind::Text, $char);
        $currentNode->appendChild($textNode);
    }

    /**
     * StartTagからElement Nodeを作成
     */
    private function createElement(StartTag $token): Node
    {
        // Elementオブジェクトを作成
        $element = new Element($token->getTag(), $token->getAttributes());

        // NodeオブジェクトでElementをラップ
        $node = new Node(NodeKind::Element, $element);

        return $node;
    }

    /**
     * スタック内に指定タグ名の要素が含まれるかチェック
     */
    private function containInStack(string $tagName): bool
    {
        foreach ($this->stackOfOpenElements as $element) {
            $elementKind = $element->getElementKind();
            if ($elementKind && $elementKind->value === $tagName) {
                return true;
            }
        }

        return false;
    }

    /**
     * 指定されたタグ名の要素までポップ（そのタグも含む）
     */
    private function popUntil(string $tagName): void
    {
        while (! empty($this->stackOfOpenElements)) {
            $element = array_pop($this->stackOfOpenElements);
            $elementKind = $element->getElementKind();
            if ($elementKind && $elementKind->value === $tagName) {
                break;
            }
        }
    }

    /**
     * 指定されたタグ名の要素まで検索（ポップはしない）
     */
    private function findInStack(string $tagName): ?Node
    {
        for ($i = count($this->stackOfOpenElements) - 1; $i >= 0; $i--) {
            $element = $this->stackOfOpenElements[$i];
            $elementKind = $element->getElementKind();
            if ($elementKind && $elementKind->value === $tagName) {
                return $element;
            }
        }

        return null;
    }

    /**
     * 現在のノード（スタックの最上位）をポップ
     */
    private function popCurrentNode(): ?Node
    {
        if (empty($this->stackOfOpenElements)) {
            return null;
        }

        return array_pop($this->stackOfOpenElements);
    }

    /**
     * 現在のノード（スタックの最上位）を取得
     */
    private function getCurrentNode(): ?Node
    {
        if (empty($this->stackOfOpenElements)) {
            return null;
        }

        return end($this->stackOfOpenElements);
    }

    /**
     * 暗黙的にhtml要素を挿入
     */
    private function insertImplicitHtmlElement(): void
    {
        $element = new Element('html');
        $htmlNode = new Node(NodeKind::Element, $element);
        $this->window->getDocument()->appendChild($htmlNode);
        $this->stackOfOpenElements[] = $htmlNode;
    }

    /**
     * 暗黙的にhead要素を挿入
     */
    private function insertImplicitHeadElement(): void
    {
        $element = new Element('head');
        $headNode = new Node(NodeKind::Element, $element);
        $this->insertElement($headNode);
    }

    /**
     * 暗黙的にbody要素を挿入
     */
    private function insertImplicitBodyElement(): void
    {
        $element = new Element('body');
        $bodyNode = new Node(NodeKind::Element, $element);
        $this->insertElement($bodyNode);
    }

    /**
     * 解析結果のWindowオブジェクトを取得
     */
    public function getWindow(): Window
    {
        return $this->window;
    }

    /**
     * 現在の挿入モードを取得
     */
    public function getMode(): InsertionMode
    {
        return $this->mode;
    }

    /**
     * 開かれた要素のスタックを取得
     */
    public function getStackOfOpenElements(): array
    {
        return $this->stackOfOpenElements;
    }
}
