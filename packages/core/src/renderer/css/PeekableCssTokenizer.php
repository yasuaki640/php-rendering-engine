<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Css;

/**
 * Peekable wrapper for CssTokenizer to provide peek functionality
 * Equivalent to Rust's Peekable iterator functionality
 */
class PeekableCssTokenizer
{
    /** @var array<int, CssToken> */
    private array $tokens;
    private int $position;

    public function __construct(CssTokenizer $tokenizer)
    {
        $this->tokens = [];
        $this->position = 0;

        // Convert iterator to array for random access
        foreach ($tokenizer as $token) {
            $this->tokens[] = $token;
        }
    }

    /**
     * Get the next token without advancing the position
     */
    public function peek(): ?CssToken
    {
        return $this->tokens[$this->position] ?? null;
    }

    /**
     * Get the next token and advance the position
     */
    public function next(): ?CssToken
    {
        $token = $this->tokens[$this->position] ?? null;
        if ($token !== null) {
            $this->position++;
        }

        return $token;
    }
}
