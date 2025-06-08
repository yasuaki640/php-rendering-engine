<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Dom;

/**
 * DOM API functions
 *
 * Rustのdom/api.rsを移植したもの
 */
class Api
{
    /**
     * 指定された要素種別のノードを取得する
     *
     * DOMツリーを深さ優先で走査し、指定されたElementKindに一致する最初のノードを返す
     *
     * @param Node|null $node 開始ノード
     * @param ElementKind $elementKind 探している要素の種別
     * @return Node|null 見つかったノード、見つからない場合はnull
     */
    public static function getTargetElementNode(?Node $node, ElementKind $elementKind): ?Node
    {
        if ($node === null) {
            return null;
        }

        // 現在のノードが目的の要素種別かチェック
        if ($node->getKind() === NodeKind::Element &&
            $node->getElementKind() === $elementKind) {
            return $node;
        }

        // 子ノードを再帰的に検索
        $result1 = self::getTargetElementNode($node->getFirstChild(), $elementKind);
        // 兄弟ノードを再帰的に検索
        $result2 = self::getTargetElementNode($node->getNextSibling(), $elementKind);

        // 両方ともnullの場合はnullを返す
        if ($result1 === null && $result2 === null) {
            return null;
        }

        // 子ノードの検索結果を優先
        if ($result1 === null) {
            return $result2;
        }

        return $result1;
    }

    /**
     * スタイル要素のコンテンツを取得する
     *
     * DOMツリーからstyle要素を見つけて、その中のテキストコンテンツを返す
     *
     * @param Node $root ルートノード
     * @return string スタイルのコンテンツ、見つからない場合は空文字列
     */
    public static function getStyleContent(Node $root): string
    {
        $styleNode = self::getTargetElementNode($root, ElementKind::Style);
        if ($styleNode === null) {
            return '';
        }

        $textNode = $styleNode->getFirstChild();
        if ($textNode === null) {
            return '';
        }

        if ($textNode->getKind() === NodeKind::Text) {
            return $textNode->getTextContent() ?? '';
        }

        return '';
    }
}
