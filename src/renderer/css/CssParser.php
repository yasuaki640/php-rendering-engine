<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Renderer\Css;

/**
 * CSS Parser based on CSS Syntax Level 3 specification
 * Equivalent to Rust's CssParser struct
 * @see https://www.w3.org/TR/css-syntax-3/
 */
class CssParser
{
    private PeekableCssTokenizer $t;

    public function __construct(CssTokenizer $tokenizer)
    {
        $this->t = new PeekableCssTokenizer($tokenizer);
    }

    /**
     * Consume a component value
     * @see https://www.w3.org/TR/css-syntax-3/#consume-component-value
     */
    private function consumeComponentValue(): CssToken
    {
        $token = $this->t->next();
        if ($token === null) {
            throw new \RuntimeException("should have a token in consume_component_value");
        }

        return $token;
    }

    /**
     * Consume an identifier token
     */
    private function consumeIdent(): string
    {
        $token = $this->t->next();
        if ($token === null) {
            throw new \RuntimeException("should have a token but got None");
        }

        if ($token->type === CssTokenType::Ident) {
            return (string) $token->value;
        }

        throw new \RuntimeException("Parse error: {$token->type->name} is an unexpected token.");
    }

    /**
     * Consume a declaration
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-declaration
     */
    private function consumeDeclaration(): ?Declaration
    {
        if ($this->t->peek() === null) {
            return null;
        }

        // Declaration構造体を初期化する
        $declaration = Declaration::new();

        // Declaration構造体のプロパティに識別子を設定する
        $declaration->setProperty($this->consumeIdent());

        // もし次のトークンがコロンでない場合、パースエラーなので、nullを返す
        $token = $this->t->next();
        if ($token === null || $token->type !== CssTokenType::Colon) {
            return null;
        }

        // Declaration構造体の値にコンポーネント値を設定する
        $declaration->setValue($this->consumeComponentValue());

        return $declaration;
    }

    /**
     * Consume a list of declarations
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-list-of-declarations
     * @return array<int, Declaration>
     */
    private function consumeListOfDeclarations(): array
    {
        $declarations = [];

        while (true) {
            $token = $this->t->peek();
            if ($token === null) {
                return $declarations;
            }

            switch ($token->type) {
                case CssTokenType::CloseCurly:
                    $this->t->next(); // consume the CloseCurly token

                    return $declarations;

                case CssTokenType::SemiColon:
                    $this->t->next(); // consume the SemiColon token

                    // 一つの宣言が終了。何もしない
                    break;

                case CssTokenType::Ident:
                    $declaration = $this->consumeDeclaration();
                    if ($declaration !== null) {
                        $declarations[] = $declaration;
                    }

                    break;

                default:
                    $this->t->next();

                    break;
            }
        }
    }

    /**
     * Consume a selector
     */
    private function consumeSelector(): Selector
    {
        $token = $this->t->next();
        if ($token === null) {
            throw new \RuntimeException("should have a token but got None");
        }

        switch ($token->type) {
            case CssTokenType::HashToken:
                // IDセレクタ: #idから#を除いた部分を返す
                $value = (string) $token->value;

                return Selector::idSelector(substr($value, 1)); // Remove the '#' prefix

            case CssTokenType::Delim:
                if ($token->value === '.') {
                    // クラスセレクタ
                    return Selector::classSelector($this->consumeIdent());
                }

                throw new \RuntimeException("Parse error: {$token->type->name} is an unexpected token.");

            case CssTokenType::Ident:
                // a:hoverのようなセレクタはタグ名のセレクタとして扱うため、
                // もしコロン（:）が出てきた場合は宣言ブロックの開始直前まで
                // トークンを進める
                $peeked = $this->t->peek();
                if ($peeked !== null && $peeked->type === CssTokenType::Colon) {
                    while (($peeked = $this->t->peek()) !== null && $peeked->type !== CssTokenType::OpenCurly) {
                        $this->t->next();
                    }
                }

                return Selector::typeSelector((string) $token->value);

            case CssTokenType::AtKeyword:
                // @から始まるルールを無視するために、宣言ブロックの開始直前まで
                // トークンを進める
                while (($peeked = $this->t->peek()) !== null && $peeked->type !== CssTokenType::OpenCurly) {
                    $this->t->next();
                }

                return Selector::unknownSelector();

            default:
                $this->t->next();

                return Selector::unknownSelector();
        }
    }

    /**
     * Consume a qualified rule
     * @see https://www.w3.org/TR/css-syntax-3/#consume-qualified-rule
     * @see https://www.w3.org/TR/css-syntax-3/#qualified-rule
     * @see https://www.w3.org/TR/css-syntax-3/#style-rules
     */
    private function consumeQualifiedRule(): ?QualifiedRule
    {
        $rule = QualifiedRule::new();

        while (true) {
            $token = $this->t->peek();
            if ($token === null) {
                return null;
            }

            if ($token->type === CssTokenType::OpenCurly) {
                $this->t->next(); // consume the OpenCurly token
                $rule->setDeclarations($this->consumeListOfDeclarations());

                return $rule;
            } else {
                $rule->setSelector($this->consumeSelector());
            }
        }
    }

    /**
     * Consume a list of rules
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-list-of-rules
     * @return array<int, QualifiedRule>
     */
    private function consumeListOfRules(): array
    {
        // 空の配列を作成する
        $rules = [];

        while (true) {
            $token = $this->t->peek();
            if ($token === null) {
                return $rules;
            }

            switch ($token->type) {
                // AtKeywordトークンが出てきた場合、他のCSSをインポートする@import、
                // メディアクエリを表す@mediaなどのルールが始まることを表す
                case CssTokenType::AtKeyword:
                    $this->consumeQualifiedRule();

                    // しかし、本書のブラウザでは@から始まるルールはサポート
                    // しないので、無視をする
                    break;

                default:
                    // 1つのルールを解釈し、配列に追加する
                    $rule = $this->consumeQualifiedRule();
                    if ($rule !== null) {
                        $rules[] = $rule;
                    } else {
                        return $rules;
                    }

                    break;
            }
        }
    }

    /**
     * Parse stylesheet
     * @see https://www.w3.org/TR/css-syntax-3/#parse-stylesheet
     */
    public function parseStylesheet(): StyleSheet
    {
        // StyleSheet構造体のインスタンスを作成する
        $sheet = StyleSheet::new();

        // トークン列からルールのリストを作成し、StyleSheetのフィールドに設定する
        $sheet->setRules($this->consumeListOfRules());

        return $sheet;
    }
}
