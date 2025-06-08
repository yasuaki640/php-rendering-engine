<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Renderer\Css;

use Iterator;

/**
 * CSS Tokenizer based on CSS Syntax Level 3 specification
 * Equivalent to Rust's CssTokenizer
 * @see https://www.w3.org/TR/css-syntax-3/
 * @implements Iterator<int, CssToken>
 */
class CssTokenizer implements Iterator
{
    private int $pos;
    /** @var array<int, string> */
    private array $input;
    private int $currentTokenIndex;
    /** @var array<int, CssToken> */
    private array $tokens;

    public function __construct(string $css)
    {
        $this->pos = 0;
        $this->input = mb_str_split($css);
        $this->currentTokenIndex = 0;
        $this->tokens = [];
        $this->tokenize();
    }

    /**
     * Tokenize the entire CSS string
     */
    private function tokenize(): void
    {
        while (($token = $this->nextToken()) !== null) {
            $this->tokens[] = $token;
        }
    }

    /**
     * Consume a string token
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-string-token
     */
    private function consumeStringToken(): string
    {
        $s = '';

        while (true) {
            if ($this->pos >= count($this->input)) {
                return $s;
            }

            $this->pos++;
            $c = $this->input[$this->pos];
            if ($c === '"' || $c === "'") {
                break;
            }
            $s .= $c;
        }

        return $s;
    }

    /**
     * Consume a numeric token
     * @see https://www.w3.org/TR/css-syntax-3/#consume-number
     * @see https://www.w3.org/TR/css-syntax-3/#consume-a-numeric-token
     */
    private function consumeNumericToken(): float
    {
        $num = 0.0;
        $floating = false;
        $floatingDigit = 1.0;

        while (true) {
            if ($this->pos >= count($this->input)) {
                return $num;
            }

            $c = $this->input[$this->pos];

            if (ctype_digit($c)) {
                if ($floating) {
                    $floatingDigit *= 1.0 / 10.0;
                    $num += (float) $c * $floatingDigit;
                } else {
                    $num = $num * 10.0 + (float) $c;
                }
                $this->pos++;
            } elseif ($c === '.') {
                $floating = true;
                $this->pos++;
            } else {
                break;
            }
        }

        return $num;
    }

    /**
     * Consume an identifier token
     * @see https://www.w3.org/TR/css-syntax-3/#consume-ident-like-token
     * @see https://www.w3.org/TR/css-syntax-3/#consume-name
     */
    private function consumeIdentToken(): string
    {
        $s = $this->input[$this->pos];

        while (true) {
            $this->pos++;
            if ($this->pos >= count($this->input)) {
                break;
            }
            $c = $this->input[$this->pos];
            if (ctype_alnum($c) || $c === '-' || $c === '_') {
                $s .= $c;
            } else {
                break;
            }
        }

        return $s;
    }

    /**
     * Get the next token
     * @see https://www.w3.org/TR/css-syntax-3/#consume-token
     */
    private function nextToken(): ?CssToken
    {
        while (true) {
            if ($this->pos >= count($this->input)) {
                return null;
            }

            $c = $this->input[$this->pos];

            $token = match ($c) {
                '(' => CssToken::openParenthesis(),
                ')' => CssToken::closeParenthesis(),
                ',' => CssToken::delim(','),
                '.' => CssToken::delim('.'),
                ':' => CssToken::colon(),
                ';' => CssToken::semiColon(),
                '{' => CssToken::openCurly(),
                '}' => CssToken::closeCurly(),
                ' ', "\n" => $this->skipWhitespace(),
                '"', "'" => $this->handleStringToken(),
                '#' => $this->handleHashToken(),
                '-' => $this->handleHyphenToken(),
                '@' => $this->handleAtToken(),
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' => $this->handleNumberToken(),
                default => $this->handleDefault($c),
            };

            if ($token === null) {
                continue;
            }

            $this->pos++;

            return $token;
        }
    }

    private function skipWhitespace(): ?CssToken
    {
        $this->pos++;

        return null;
    }

    private function handleStringToken(): CssToken
    {
        $value = $this->consumeStringToken();

        return CssToken::stringToken($value);
    }

    private function handleHashToken(): CssToken
    {
        // 本書では、常に #ID の形式のIDセレクタとして扱う。
        $value = $this->consumeIdentToken();
        $this->pos--;

        return CssToken::hashToken($value);
    }

    private function handleHyphenToken(): CssToken
    {
        // 本書では、負の数は取り扱わないため、ハイフンは識別子の一つとして扱う。
        $token = CssToken::ident($this->consumeIdentToken());
        $this->pos--;

        return $token;
    }

    private function handleAtToken(): CssToken
    {
        // 次の3文字が識別子として有効な文字の場合、<at-keyword-token>
        // トークンを作成して返す。
        // それ以外の場合、<delim-token>を返す。
        if (isset($this->input[$this->pos + 1], $this->input[$this->pos + 2], $this->input[$this->pos + 3]) &&
            ctype_alpha($this->input[$this->pos + 1]) &&
            ctype_alnum($this->input[$this->pos + 2]) &&
            ctype_alnum($this->input[$this->pos + 3])) {
            // skip '@'
            $this->pos++;
            $token = CssToken::atKeyword($this->consumeIdentToken());
            $this->pos--;

            return $token;
        } else {
            return CssToken::delim('@');
        }
    }

    private function handleNumberToken(): CssToken
    {
        $token = CssToken::number($this->consumeNumericToken());
        $this->pos--;

        return $token;
    }

    private function handleDefault(string $c): CssToken
    {
        if (ctype_alpha($c) || $c === '_') {
            $token = CssToken::ident($this->consumeIdentToken());
            $this->pos--;

            return $token;
        } else {
            throw new \RuntimeException("char {$c} is not supported yet");
        }
    }

    // Iterator interface implementation

    public function current(): CssToken
    {
        return $this->tokens[$this->currentTokenIndex];
    }

    public function key(): int
    {
        return $this->currentTokenIndex;
    }

    public function next(): void
    {
        $this->currentTokenIndex++;
    }

    public function rewind(): void
    {
        $this->currentTokenIndex = 0;
    }

    public function valid(): bool
    {
        return isset($this->tokens[$this->currentTokenIndex]);
    }

    /**
     * Get all tokens as an array
     * @return array<int, CssToken>
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
