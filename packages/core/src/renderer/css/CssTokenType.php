<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Css;

/**
 * CSS Token Type enumeration
 * @see https://www.w3.org/TR/css-syntax-3/
 */
enum CssTokenType: string
{
    /// https://www.w3.org/TR/css-syntax-3/#typedef-hash-token
    case HashToken = 'hash';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-delim-token
    case Delim = 'delim';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-number-token
    case Number = 'number';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-colon-token
    case Colon = 'colon';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-semicolon-token
    case SemiColon = 'semicolon';
    /// https://www.w3.org/TR/css-syntax-3/#tokendef-open-paren
    case OpenParenthesis = 'open_parenthesis';
    /// https://www.w3.org/TR/css-syntax-3/#tokendef-close-paren
    case CloseParenthesis = 'close_parenthesis';
    /// https://www.w3.org/TR/css-syntax-3/#tokendef-open-curly
    case OpenCurly = 'open_curly';
    /// https://www.w3.org/TR/css-syntax-3/#tokendef-close-curly
    case CloseCurly = 'close_curly';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-ident-token
    case Ident = 'ident';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-string-token
    case StringToken = 'string';
    /// https://www.w3.org/TR/css-syntax-3/#typedef-at-keyword-token
    case AtKeyword = 'at_keyword';

    /**
     * Get the default value for tokens without custom values
     */
    public function getDefaultValue(): string
    {
        return match ($this) {
            self::Colon => ':',
            self::SemiColon => ';',
            self::OpenParenthesis => '(',
            self::CloseParenthesis => ')',
            self::OpenCurly => '{',
            self::CloseCurly => '}',
            default => '',
        };
    }
}
