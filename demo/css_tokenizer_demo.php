<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssTokenizer;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssToken;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Css\CssTokenType;

/**
 * CSSトークナイザーデモスクリプト
 * CSSスタイルシート文字列をトークン列として出力します
 */

function tokenizeCss(string $css): void
{
    echo "CSS入力: " . $css . "\n";
    echo "========================\n";
    
    $tokenizer = new CssTokenizer($css);
    $tokenIndex = 0;
    
    foreach ($tokenizer as $token) {
        $tokenIndex++;
        echo sprintf("[%02d] ", $tokenIndex);
        
        $type = $token->type;
        $value = $token->value;
        
        switch ($type) {
            case CssTokenType::Ident:
                echo "Ident: '{$value}'\n";
                break;
                
            case CssTokenType::OpenCurly:
                echo "OpenCurly: '{'\n";
                break;
                
            case CssTokenType::CloseCurly:
                echo "CloseCurly: '}'\n";
                break;
                
            case CssTokenType::Colon:
                echo "Colon: ':'\n";
                break;
                
            case CssTokenType::SemiColon:
                echo "SemiColon: ';'\n";
                break;
                
            case CssTokenType::Number:
                echo "Number: {$value}\n";
                break;
                
            case CssTokenType::StringToken:
                echo "String: \"{$value}\"\n";
                break;
                
            case CssTokenType::HashToken:
                echo "Hash: '#{$value}'\n";
                break;
                
            case CssTokenType::AtKeyword:
                echo "AtKeyword: '@{$value}'\n";
                break;
                
            case CssTokenType::Delim:
                $displayChar = match ($value) {
                    ' ' => '[SPACE]',
                    "\n" => '[NEWLINE]',
                    "\t" => '[TAB]',
                    default => $value
                };
                echo "Delim: '{$displayChar}'\n";
                break;
                
            case CssTokenType::OpenParenthesis:
                echo "OpenParenthesis: '('\n";
                break;
                
            case CssTokenType::CloseParenthesis:
                echo "CloseParenthesis: ')'\n";
                break;
                
            default:
                echo "Unknown token type: {$type->value}\n";
                break;
        }
    }
    
    echo "========================\n\n";
}

// デモ実行
echo "CSSトークナイザーデモ\n";
echo "====================\n\n";

// 1. 基本的なCSSルール
tokenizeCss('p { color: red; }');

// 2. 複数のプロパティ
tokenizeCss('h1 { color: blue; font-size: 16px; }');

// 3. クラスセレクタ
tokenizeCss('.container { width: 100px; height: 200px; }');

// 4. IDセレクタ
tokenizeCss('#header { background-color: #ff0000; }');

// 5. 複数のセレクタ
tokenizeCss('h1, h2, h3 { margin: 0; padding: 0; }');

// 6. 数値の単位付き
tokenizeCss('body { margin: 10px; font-size: 1.2em; }');

// 7. 文字列値
tokenizeCss('body { font-family: "Arial", sans-serif; }');

// 8. より複雑なCSS（改行付き）
tokenizeCss('.header { background-color: #ff0000; border: 1px solid black; }');

// 9. @ルール（基本的なもの）
tokenizeCss('@media screen { body { font-size: 14px; } }');

// 10. フレックスボックス（基本的なプロパティ）
tokenizeCss('.flex { display: flex; align-items: center; }');

// 11. 実際のサンプル（簡易版）
tokenizeCss('h1 { color: black; font-size: 24px; }');
