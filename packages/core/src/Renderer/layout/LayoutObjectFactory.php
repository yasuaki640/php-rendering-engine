<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Layout;

class LayoutObjectFactory
{
    public static function createLayoutObject(
        ?object $node,
        ?LayoutObject $parentObj,
        object $cssom
    ): ?LayoutObject {
        if ($node === null) {
            return null;
        }

        // LayoutObjectを作成する
        $layoutObject = new LayoutObject($node, $parentObj);

        // CSSのルールをセレクタで選択されたノードに適用する
        if (method_exists($cssom, 'getRules')) {
            $rules = $cssom->getRules();
            foreach ($rules as $rule) {
                if ($layoutObject->isNodeSelected($rule->getSelector())) {
                    $layoutObject->cascadingStyle($rule->getDeclarations());
                }
            }
        }

        // CSSでスタイルが指定されていない場合、デフォルトの値または親のノードから継承した値を使用する
        $parentStyle = $parentObj !== null ? $parentObj->getStyle() : null;
        $layoutObject->defaultingStyle($node, $parentStyle);

        // displayプロパティがnoneの場合、ノードを作成しない
        if ($layoutObject->getStyle()->getDisplay() === DisplayType::DisplayNone) {
            return null;
        }

        // displayプロパティの最終的な値を使用してノードの種類を決定する
        $layoutObject->updateKind();

        return $layoutObject;
    }
}
