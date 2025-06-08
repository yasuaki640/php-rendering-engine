<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Renderer\Css;

/**
 * CSS Token representation based on CSS Syntax Level 3 specification
 * Equivalent to Rust's CssToken enum
 * @see https://www.w3.org/TR/css-syntax-3/
 */
readonly class CssToken
{
    public function __construct(
        public CssTokenType $type,
        public string|float|null $value = null,
    ) {}

    /**
     * Create a HashToken
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-hash-token
     */
    public static function hashToken(string $value): self
    {
        return new self(CssTokenType::HashToken, $value);
    }

    /**
     * Create a Delim token
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-delim-token
     */
    public static function delim(string $char): self
    {
        return new self(CssTokenType::Delim, $char);
    }

    /**
     * Create a Number token
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-number-token
     */
    public static function number(float $value): self
    {
        return new self(CssTokenType::Number, $value);
    }

    /**
     * Create a Colon token
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-colon-token
     */
    public static function colon(): self
    {
        return new self(CssTokenType::Colon);
    }

    /**
     * Create a SemiColon token
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-semicolon-token
     */
    public static function semiColon(): self
    {
        return new self(CssTokenType::SemiColon);
    }

    /**
     * Create an OpenParenthesis token
     * @see https://www.w3.org/TR/css-syntax-3/#tokendef-open-paren
     */
    public static function openParenthesis(): self
    {
        return new self(CssTokenType::OpenParenthesis);
    }

    /**
     * Create a CloseParenthesis token
     * @see https://www.w3.org/TR/css-syntax-3/#tokendef-close-paren
     */
    public static function closeParenthesis(): self
    {
        return new self(CssTokenType::CloseParenthesis);
    }

    /**
     * Create an OpenCurly token
     * @see https://www.w3.org/TR/css-syntax-3/#tokendef-open-curly
     */
    public static function openCurly(): self
    {
        return new self(CssTokenType::OpenCurly);
    }

    /**
     * Create a CloseCurly token
     * @see https://www.w3.org/TR/css-syntax-3/#tokendef-close-curly
     */
    public static function closeCurly(): self
    {
        return new self(CssTokenType::CloseCurly);
    }

    /**
     * Create an Ident token
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-ident-token
     */
    public static function ident(string $value): self
    {
        return new self(CssTokenType::Ident, $value);
    }

    /**
     * Create a StringToken
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-string-token
     */
    public static function stringToken(string $value): self
    {
        return new self(CssTokenType::StringToken, $value);
    }

    /**
     * Create an AtKeyword token
     * @see https://www.w3.org/TR/css-syntax-3/#typedef-at-keyword-token
     */
    public static function atKeyword(string $value): self
    {
        return new self(CssTokenType::AtKeyword, $value);
    }

    /**
     * Get the token value
     */
    public function getValue(): string|float
    {
        return $this->value ?? $this->type->getDefaultValue();
    }

    /**
     * Check if this token equals another token
     */
    public function equals(CssToken $other): bool
    {
        return $this->type === $other->type && $this->value === $other->value;
    }
}
