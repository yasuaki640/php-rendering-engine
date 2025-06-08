<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Layout;

use MyApp\Core\Renderer\Css\Selector;
use MyApp\Core\Renderer\Css\SelectorType;
use MyApp\Core\Renderer\Dom\Element;
use MyApp\Core\Renderer\Dom\Node;
use MyApp\Core\Renderer\Dom\NodeKind;

class LayoutObject
{
    private LayoutObjectKind $kind;
    private Node $node;
    private ?LayoutObject $firstChild;
    private ?LayoutObject $nextSibling;
    private ?LayoutObject $parent;
    private ComputedStyle $style;
    private LayoutPoint $point;
    private LayoutSize $size;

    public function __construct(Node $node, ?LayoutObject $parentObj = null)
    {
        $this->kind = LayoutObjectKind::Block;
        $this->node = $node;
        $this->firstChild = null;
        $this->nextSibling = null;
        $this->parent = $parentObj;
        $this->style = new ComputedStyle();
        $this->point = new LayoutPoint(0, 0);
        $this->size = new LayoutSize(0, 0);
    }

    /**
     * @return DisplayItem[]
     */
    public function paint(): array
    {
        if ($this->style->getDisplay() === DisplayType::DisplayNone) {
            return [];
        }

        $items = [];

        switch ($this->kind) {
            case LayoutObjectKind::Block:
                // (d1)
                $nodeKind = $this->getNodeKind();
                if (is_object($nodeKind)) {
                    $items[] = DisplayItem::createRect(
                        $this->style,
                        $this->point,
                        $this->size
                    );
                }

                break;

            case LayoutObjectKind::Inline:
                // (d2)
                // 本書のブラウザでは、描画するインライン要素はない。
                // <img>タグなどをサポートした場合はこのケースの中で処理をする
                break;

            case LayoutObjectKind::Text:
                // (d3)
                $nodeKind = $this->getNodeKind();
                if (is_string($nodeKind)) {
                    $ratio = match ($this->style->getFontSize()) {
                        FontSize::Medium => 1,
                        FontSize::XLarge => 2,
                        FontSize::XXLarge => 3,
                    };

                    $plainText = str_replace("\n", " ", $nodeKind);
                    $plainText = implode(" ", array_filter(explode(" ", $plainText), fn ($s) => ! empty($s)));
                    $lines = $this->splitText($plainText, LayoutConstants::CHAR_WIDTH * $ratio);

                    $i = 0;
                    foreach ($lines as $line) {
                        $items[] = DisplayItem::createText(
                            $line,
                            $this->style,
                            new LayoutPoint(
                                $this->point->getX(),
                                $this->point->getY() + LayoutConstants::CHAR_HEIGHT_WITH_PADDING * $i
                            )
                        );
                        $i++;
                    }
                }

                break;
        }

        return $items;
    }

    public function computeSize(LayoutSize $parentSize): void
    {
        $size = new LayoutSize(0, 0);

        switch ($this->kind) {
            case LayoutObjectKind::Block:
                $size->setWidth($parentSize->getWidth());

                // 全ての子ノードの高さを足し合わせた結果が高さになる。
                // ただし、インライン要素が横に並んでいる場合は注意が必要
                $height = 0;
                $child = $this->firstChild;
                $previousChildKind = LayoutObjectKind::Block;

                while ($child !== null) {
                    if ($previousChildKind === LayoutObjectKind::Block || $child->getKind() === LayoutObjectKind::Block) {
                        $height += $child->getSize()->getHeight();
                    }

                    $previousChildKind = $child->getKind();
                    $child = $child->getNextSibling();
                }
                $size->setHeight($height);

                break;

            case LayoutObjectKind::Inline:
                // 全ての子ノードの高さと横幅を足し合わせた結果が現在のノードの高さと横幅とになる
                $width = 0;
                $height = 0;
                $child = $this->firstChild;

                while ($child !== null) {
                    $width += $child->getSize()->getWidth();
                    $height += $child->getSize()->getHeight();
                    $child = $child->getNextSibling();
                }

                $size->setWidth($width);
                $size->setHeight($height);

                break;

            case LayoutObjectKind::Text:
                $nodeKind = $this->getNodeKind();
                if (is_string($nodeKind)) {
                    $ratio = match ($this->style->getFontSize()) {
                        FontSize::Medium => 1,
                        FontSize::XLarge => 2,
                        FontSize::XXLarge => 3,
                    };

                    $width = LayoutConstants::CHAR_WIDTH * $ratio * strlen($nodeKind);
                    if ($width > LayoutConstants::CONTENT_AREA_WIDTH) {
                        // テキストが複数行のとき
                        $size->setWidth(LayoutConstants::CONTENT_AREA_WIDTH);
                        $lineNum = $width % LayoutConstants::CONTENT_AREA_WIDTH === 0
                            ? intval($width / LayoutConstants::CONTENT_AREA_WIDTH)
                            : intval($width / LayoutConstants::CONTENT_AREA_WIDTH) + 1;
                        $size->setHeight(LayoutConstants::CHAR_HEIGHT_WITH_PADDING * $ratio * $lineNum);
                    } else {
                        // テキストが1行に収まるとき
                        $size->setWidth($width);
                        $size->setHeight(LayoutConstants::CHAR_HEIGHT_WITH_PADDING * $ratio);
                    }
                }

                break;
        }

        $this->size = $size;
    }

    public function computePosition(
        LayoutPoint $parentPoint,
        LayoutObjectKind $previousSiblingKind,
        ?LayoutPoint $previousSiblingPoint,
        ?LayoutSize $previousSiblingSize
    ): void {
        $point = new LayoutPoint(0, 0);

        // もしブロック要素が兄弟ノードの場合、Y軸方向に進む
        if ($this->kind === LayoutObjectKind::Block || $previousSiblingKind === LayoutObjectKind::Block) {
            if ($previousSiblingSize !== null && $previousSiblingPoint !== null) {
                $point->setY($previousSiblingPoint->getY() + $previousSiblingSize->getHeight());
            } else {
                $point->setY($parentPoint->getY());
            }
            $point->setX($parentPoint->getX());
        }
        // もしインライン要素が並ぶ場合、X軸方向に進む
        elseif ($this->kind === LayoutObjectKind::Inline && $previousSiblingKind === LayoutObjectKind::Inline) {
            if ($previousSiblingSize !== null && $previousSiblingPoint !== null) {
                $point->setX($previousSiblingPoint->getX() + $previousSiblingSize->getWidth());
                $point->setY($previousSiblingPoint->getY());
            } else {
                $point->setX($parentPoint->getX());
                $point->setY($parentPoint->getY());
            }
        } else {
            $point->setX($parentPoint->getX());
            $point->setY($parentPoint->getY());
        }

        $this->point = $point;
    }

    public function isNodeSelected(Selector $selector): bool
    {
        $nodeKind = $this->getNodeKind();

        // ノードがElement型の場合のみ処理を行う
        if ($nodeKind instanceof Element) {
            switch ($selector->type) {
                case SelectorType::TypeSelector:
                    // タグ名でマッチング（例：div, p, h1）
                    return $nodeKind->getKind()->value === $selector->value;

                case SelectorType::ClassSelector:
                    // class属性でマッチング（例：.class-name）
                    foreach ($nodeKind->getAttributes() as $attribute) {
                        if ($attribute->name === 'class' && $attribute->value === $selector->value) {
                            return true;
                        }
                    }

                    return false;

                case SelectorType::IdSelector:
                    // id属性でマッチング（例：#id-name）
                    foreach ($nodeKind->getAttributes() as $attribute) {
                        if ($attribute->name === 'id' && $attribute->value === $selector->value) {
                            return true;
                        }
                    }

                    return false;

                case SelectorType::UnknownSelector:
                    return false;

                default:
                    return false;
            }
        }

        return false;
    }

    public function cascadingStyle(array $declarations): void
    {
        foreach ($declarations as $declaration) {
            $property = $declaration->property ?? '';
            $value = $declaration->value ?? null;

            switch ($property) {
                case 'background-color':
                    try {
                        $valueStr = $value ? $value->value : '';
                        if (str_starts_with($valueStr, '#')) {
                            $color = Color::fromCode($valueStr);
                        } else {
                            $color = Color::fromName($valueStr);
                        }
                        $this->style->setBackgroundColor($color);
                    } catch (\Exception $e) {
                        // エラーの場合は白色を使用
                        $this->style->setBackgroundColor(Color::white());
                    }

                    break;

                case 'color':
                    try {
                        $valueStr = $value ? $value->value : '';
                        if (str_starts_with($valueStr, '#')) {
                            $color = Color::fromCode($valueStr);
                        } else {
                            $color = Color::fromName($valueStr);
                        }
                        $this->style->setColor($color);
                    } catch (\Exception $e) {
                        // エラーの場合は黒色を使用
                        $this->style->setColor(Color::black());
                    }

                    break;

                case 'display':
                    try {
                        $valueStr = $value ? $value->value : '';
                        $displayType = DisplayType::fromString($valueStr);
                        $this->style->setDisplay($displayType);
                    } catch (\Exception $e) {
                        $this->style->setDisplay(DisplayType::DisplayNone);
                    }

                    break;
            }
        }
    }

    public function defaultingStyle(?object $node, ?ComputedStyle $parentStyle): void
    {
        $this->style->defaulting($node, $parentStyle);
    }

    public function updateKind(): void
    {
        $nodeKind = $this->node->getKind();

        switch ($nodeKind) {
            case NodeKind::Document:
                throw new \RuntimeException('should not create a layout object for a Document node');

            case NodeKind::Element:
                $display = $this->style->getDisplay();
                switch ($display) {
                    case DisplayType::Block:
                        $this->kind = LayoutObjectKind::Block;

                        break;
                    case DisplayType::Inline:
                        $this->kind = LayoutObjectKind::Inline;

                        break;
                    case DisplayType::DisplayNone:
                        throw new \RuntimeException('should not create a layout object for display:none');
                }

                break;

            case NodeKind::Text:
                $this->kind = LayoutObjectKind::Text;

                break;
        }
    }

    // Getters and Setters
    public function getKind(): LayoutObjectKind
    {
        return $this->kind;
    }

    /**
     * LayoutObjectの等価性を判定する（RustのPartialEq実装に対応）
     * kindフィールドのみで比較を行う
     */
    public function equals(LayoutObject $other): bool
    {
        return $this->kind === $other->kind;
    }

    public function getNodeKind(): mixed
    {
        $nodeKind = $this->node->getKind();

        switch ($nodeKind) {
            case NodeKind::Document:
                return NodeKind::Document;
            case NodeKind::Element:
                return $this->node->getElement();
            case NodeKind::Text:
                return $this->node->getTextContent();
        }

        return null;
    }

    public function setFirstChild(?LayoutObject $firstChild): void
    {
        $this->firstChild = $firstChild;
    }

    public function getFirstChild(): ?LayoutObject
    {
        return $this->firstChild;
    }

    public function setNextSibling(?LayoutObject $nextSibling): void
    {
        $this->nextSibling = $nextSibling;
    }

    public function getNextSibling(): ?LayoutObject
    {
        return $this->nextSibling;
    }

    public function getParent(): ?LayoutObject
    {
        return $this->parent;
    }

    public function getStyle(): ComputedStyle
    {
        return $this->style;
    }

    public function getPoint(): LayoutPoint
    {
        return $this->point;
    }

    public function getSize(): LayoutSize
    {
        return $this->size;
    }

    /**
     * @return string[]
     */
    private function splitText(string $line, int $charWidth): array
    {
        $result = [];
        $maxWidth = LayoutConstants::WINDOW_WIDTH + LayoutConstants::WINDOW_PADDING;

        if (strlen($line) * $charWidth > $maxWidth) {
            $maxIndex = intval($maxWidth / $charWidth);
            $breakIndex = $this->findIndexForLineBreak($line, $maxIndex);
            $firstPart = substr($line, 0, $breakIndex);
            $secondPart = trim(substr($line, $breakIndex));

            $result[] = $firstPart;
            $result = array_merge($result, $this->splitText($secondPart, $charWidth));
        } else {
            $result[] = $line;
        }

        return $result;
    }

    private function findIndexForLineBreak(string $line, int $maxIndex): int
    {
        for ($i = $maxIndex - 1; $i >= 0; $i--) {
            if ($line[$i] === ' ') {
                return $i;
            }
        }

        return $maxIndex;
    }
}
