<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\HtmlTokenizer;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\StartTag;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\EndTag;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\CharToken;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Token\EofToken;

/**
 * HTMLトークナイザーデモスクリプト
 * 簡単なHTML文書文字列をトークン列として出力します
 */

function tokenizeHtml(string $html): void
{
    echo "HTML入力: " . $html . "\n";
    echo "========================\n";
    
    $tokenizer = new HtmlTokenizer($html);
    $tokenIndex = 0;
    
    while (($token = $tokenizer->next()) !== null) {
        $tokenIndex++;
        echo sprintf("[%02d] ", $tokenIndex);
        
        switch ($token->getType()) {
            case 'StartTag':
                /** @var StartTag $token */
                $output = "StartTag: <{$token->getTag()}";
                
                if (!empty($token->getAttributes())) {
                    foreach ($token->getAttributes() as $attr) {
                        $output .= " {$attr->name}=\"{$attr->value}\"";
                    }
                }
                
                if ($token->isSelfClosing()) {
                    $output .= " />";
                } else {
                    $output .= ">";
                }
                
                echo $output . "\n";
                break;
                
            case 'EndTag':
                /** @var EndTag $token */
                echo "EndTag: </{$token->getTag()}>\n";
                break;
                
            case 'Char':
                /** @var CharToken $token */
                $char = $token->getChar();
                $displayChar = match ($char) {
                    ' ' => '[SPACE]',
                    "\n" => '[NEWLINE]',
                    "\t" => '[TAB]',
                    default => $char
                };
                echo "Char: '{$displayChar}'\n";
                break;
                
            case 'Eof':
                echo "EOF\n";
                break;
                
            default:
                echo "Unknown token type: {$token->getType()}\n";
                break;
        }
    }
    
    echo "========================\n\n";
}

// デモ実行
echo "HTMLトークナイザーデモ\n";
echo "=====================\n\n";

// 1. 基本的なp要素
tokenizeHtml('<p>hoge</p>');

// 2. 属性付きのp要素
tokenizeHtml('<p class="test" id="example">Hello World</p>');

// 3. 自己終了タグ
tokenizeHtml('<img src="image.jpg" alt="test" />');

// 4. ネストした要素
tokenizeHtml('<div><p>Hello</p><span>World</span></div>');

// 5. より複雑なHTML
tokenizeHtml('<html><head><title>Test</title></head><body><h1>Hello</h1><p>World</p></body></html>');

// 6. 空白文字を含むHTML
tokenizeHtml("<div>\n  <p>Text with spaces</p>\n</div>");

// 7. scriptタグ（特別な処理が必要）
tokenizeHtml('<script>console.log("Hello");</script>');

// 8. 複数の属性のパターン
tokenizeHtml('<input type="text" placeholder=\'Enter name\' required>');
