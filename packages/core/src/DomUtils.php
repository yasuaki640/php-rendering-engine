<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core;

use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom\NodeKind;

/**
 * DOM操作のユーティリティクラス
 */
class DomUtils
{
    /**
     * DOMツリーを文字列に変換（デバッグ用）
     *
     * Rustのutils.rsのconvert_dom_to_string関数を参考に実装
     */
    public static function convertDomToString(?Node $root): string
    {
        $result = "\n";
        self::convertDomToStringInternal($root, 0, $result);

        return $result;
    }

    /**
     * DOMツリーを文字列に変換する内部処理
     */
    private static function convertDomToStringInternal(?Node $node, int $depth, string &$result): void
    {
        if ($node === null) {
            return;
        }

        // インデントを追加
        $result .= str_repeat('  ', $depth);

        // ノードの情報を文字列として追加
        $nodeInfo = self::formatNodeInfo($node);
        $result .= $nodeInfo . "\n";

        // 子ノードを再帰的に処理
        self::convertDomToStringInternal($node->getFirstChild(), $depth + 1, $result);

        // 兄弟ノードを処理
        self::convertDomToStringInternal($node->getNextSibling(), $depth, $result);
    }

    /**
     * ノードの情報をフォーマット
     */
    private static function formatNodeInfo(Node $node): string
    {
        return match ($node->getKind()) {
            NodeKind::Document => 'Document',
            NodeKind::Element => self::formatElementInfo($node),
            NodeKind::Text => self::formatTextInfo($node),
        };
    }

    /**
     * 要素ノードの情報をフォーマット
     */
    private static function formatElementInfo(Node $node): string
    {
        $elementKind = $node->getElementKind();
        if ($elementKind === null) {
            return 'Element(unknown)';
        }

        $element = $node->getElement();
        if ($element === null) {
            return sprintf('Element(%s)', $elementKind->value);
        }

        // 属性情報を取得
        $attributes = $element->getAttributes();
        if (empty($attributes)) {
            return sprintf('Element(%s)', $elementKind->value);
        }

        // 属性を文字列として整形
        $attributeStrings = [];
        foreach ($attributes as $attribute) {
            if ($attribute->value !== '') {
                $attributeStrings[] = sprintf('%s="%s"', $attribute->name, $attribute->value);
            } else {
                $attributeStrings[] = $attribute->name;
            }
        }

        $attributesStr = implode(' ', $attributeStrings);

        return sprintf('Element(%s %s)', $elementKind->value, $attributesStr);
    }

    /**
     * テキストノードの情報をフォーマット
     */
    private static function formatTextInfo(Node $node): string
    {
        $textContent = $node->getTextContent();
        if ($textContent === null || trim($textContent) === '') {
            return 'Text("")';
        }

        // 改行や余分な空白を削除して表示
        $cleanText = preg_replace('/\s+/', ' ', trim($textContent));
        $cleanText = mb_substr($cleanText, 0, 50); // 長すぎる場合は切り詰め

        return sprintf('Text("%s")', $cleanText);
    }
}
