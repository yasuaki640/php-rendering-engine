<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

class LayoutView
{
    private ?LayoutObject $root;

    public function __construct(?object $root, object $cssom)
    {
        // レイアウトツリーは描画される要素だけを持つツリーなので、<body>タグを取得し、その子要素以下を
        // レイアウトツリーのノードに変換する。
        $bodyRoot = $this->getTargetElementNode($root, 'body');

        $this->root = $this->buildLayoutTree($bodyRoot, null, $cssom);
        $this->updateLayout();
    }

    private function buildLayoutTree(?object $node, ?LayoutObject $parentObj, object $cssom): ?LayoutObject
    {
        // `LayoutObjectFactory::createLayoutObject`関数によって、ノードとなるLayoutObjectの作成を試みる。
        // CSSによって"display:none"が指定されていた場合、ノードは作成されない
        $targetNode = $node;
        $layoutObject = LayoutObjectFactory::createLayoutObject($node, $parentObj, $cssom);

        // もしノードが作成されなかった場合、DOMノードの兄弟ノードを使用してLayoutObjectの
        // 作成を試みる。LayoutObjectが作成されるまで、兄弟ノードを辿り続ける
        while ($layoutObject === null) {
            if ($targetNode !== null && method_exists($targetNode, 'getNextSibling')) {
                $targetNode = $targetNode->getNextSibling();
                $layoutObject = LayoutObjectFactory::createLayoutObject($targetNode, $parentObj, $cssom);
            } else {
                // もし兄弟ノードがない場合、処理するべきDOMツリーは終了したので、今まで
                // 作成したレイアウトツリーを返す
                return $layoutObject;
            }
        }

        if ($targetNode !== null) {
            $originalFirstChild = method_exists($targetNode, 'getFirstChild') ? $targetNode->getFirstChild() : null;
            $originalNextSibling = method_exists($targetNode, 'getNextSibling') ? $targetNode->getNextSibling() : null;

            $firstChild = $this->buildLayoutTree($originalFirstChild, $layoutObject, $cssom);
            $nextSibling = $this->buildLayoutTree($originalNextSibling, null, $cssom);

            // もし子ノードに"display:none"が指定されていた場合、LayoutObjectは作成され
            // ないため、子ノードの兄弟ノードを使用してLayoutObjectの作成を試みる。
            // LayoutObjectが作成されるか、辿るべき兄弟ノードがなくなるまで処理を繰り返す
            if ($firstChild === null && $originalFirstChild !== null) {
                $originalDomNode = method_exists($originalFirstChild, 'getNextSibling')
                    ? $originalFirstChild->getNextSibling()
                    : null;

                while ($originalDomNode !== null) {
                    $firstChild = $this->buildLayoutTree($originalDomNode, $layoutObject, $cssom);

                    if ($firstChild === null && method_exists($originalDomNode, 'getNextSibling')) {
                        $originalDomNode = $originalDomNode->getNextSibling();

                        continue;
                    }

                    break;
                }
            }

            // もし兄弟ノードに"display:none"が指定されていた場合、LayoutObjectは作成され
            // ないため、兄弟ノードの兄弟ノードを使用してLayoutObjectの作成を試みる。
            // LayoutObjectが作成されるか、辿るべき兄弟ノードがなくなるまで処理を繰り返す
            if ($nextSibling === null && $originalNextSibling !== null) {
                $originalDomNode = method_exists($originalNextSibling, 'getNextSibling')
                    ? $originalNextSibling->getNextSibling()
                    : null;

                while ($originalDomNode !== null) {
                    $nextSibling = $this->buildLayoutTree($originalDomNode, null, $cssom);

                    if ($nextSibling === null && method_exists($originalDomNode, 'getNextSibling')) {
                        $originalDomNode = $originalDomNode->getNextSibling();

                        continue;
                    }

                    break;
                }
            }

            /** @var LayoutObject $layoutObject */
            $layoutObject->setFirstChild($firstChild);
            /** @var LayoutObject $layoutObject */
            $layoutObject->setNextSibling($nextSibling);
        }

        return $layoutObject;
    }

    private static function calculateNodeSize(?LayoutObject $node, LayoutSize $parentSize): void
    {
        if ($node === null) {
            return;
        }

        // ノードがブロック要素の場合、子ノードのレイアウトを計算する前に横幅を決める
        if ($node->getKind() === LayoutObjectKind::Block) {
            $node->computeSize($parentSize);
        }

        $firstChild = $node->getFirstChild();
        self::calculateNodeSize($firstChild, $node->getSize());

        $nextSibling = $node->getNextSibling();
        self::calculateNodeSize($nextSibling, $parentSize);

        // 子ノードのサイズが決まった後にサイズを計算する。
        // ブロック要素のとき、高さは子ノードの高さに依存する
        // インライン要素のとき、高さも横幅も子ノードに依存する
        $node->computeSize($parentSize);
    }

    private static function calculateNodePosition(
        ?LayoutObject $node,
        LayoutPoint $parentPoint,
        LayoutObjectKind $previousSiblingKind,
        ?LayoutPoint $previousSiblingPoint,
        ?LayoutSize $previousSiblingSize
    ): void {
        if ($node === null) {
            return;
        }

        $node->computePosition(
            $parentPoint,
            $previousSiblingKind,
            $previousSiblingPoint,
            $previousSiblingSize
        );

        // ノード（node）の子ノードの位置を計算をする
        $firstChild = $node->getFirstChild();
        self::calculateNodePosition(
            $firstChild,
            $node->getPoint(),
            LayoutObjectKind::Block,
            null,
            null
        );

        // ノード（node）の兄弟ノードの位置を計算する
        $nextSibling = $node->getNextSibling();
        self::calculateNodePosition(
            $nextSibling,
            $parentPoint,
            $node->getKind(),
            $node->getPoint(),
            $node->getSize()
        );
    }

    private function updateLayout(): void
    {
        self::calculateNodeSize($this->root, new LayoutSize(LayoutConstants::CONTENT_AREA_WIDTH, 0));

        self::calculateNodePosition(
            $this->root,
            new LayoutPoint(0, 0),
            LayoutObjectKind::Block,
            null,
            null
        );
    }

    private static function paintNode(?LayoutObject $node, array &$displayItems): void
    {
        if ($node === null) {
            return;
        }

        $items = $node->paint();
        $displayItems = array_merge($displayItems, $items);

        $firstChild = $node->getFirstChild();
        self::paintNode($firstChild, $displayItems);

        $nextSibling = $node->getNextSibling();
        self::paintNode($nextSibling, $displayItems);
    }

    /**
     * @return DisplayItem[]
     */
    public function paint(): array
    {
        $displayItems = [];
        self::paintNode($this->root, $displayItems);

        return $displayItems;
    }

    public function getRoot(): ?LayoutObject
    {
        return $this->root;
    }

    private function getTargetElementNode(?object $root, string $elementKind): ?object
    {
        // 実際の実装は DOMのAPIに依存するため、ここでは簡略化
        // get_target_element_node関数の代替
        if ($root === null) {
            return null;
        }

        // 実際の実装では、DOMツリーを辿ってbody要素を見つける処理が必要
        return $root;
    }
}
