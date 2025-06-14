<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Renderer\Dom;

/**
 * DOMノード
 *
 * @see https://dom.spec.whatwg.org/#interface-node
 */
class Node
{
    private NodeKind $kind;
    /** @var \WeakReference<Window>|null */
    private ?\WeakReference $window = null;
    /** @var \WeakReference<Node>|null */
    private ?\WeakReference $parent = null;
    private ?Node $firstChild = null;
    /** @var \WeakReference<Node>|null */
    private ?\WeakReference $lastChild = null;
    /** @var \WeakReference<Node>|null */
    private ?\WeakReference $previousSibling = null;
    private ?Node $nextSibling = null;

    // NodeKind固有のデータ
    private ?Element $element = null;
    private ?string $textContent = null;

    public function __construct(NodeKind $kind, Element|string|null $data = null)
    {
        $this->kind = $kind;

        match ($kind) {
            NodeKind::Document => null,
            NodeKind::Element => $this->element = $data instanceof Element ? $data : null,
            NodeKind::Text => $this->textContent = is_string($data) ? $data : '',
        };
    }

    public function getKind(): NodeKind
    {
        return $this->kind;
    }

    public function setWindow(Window $window): void
    {
        $this->window = \WeakReference::create($window);
    }

    public function getWindow(): ?Window
    {
        return $this->window?->get();
    }

    public function setParent(?Node $parent): void
    {
        $this->parent = $parent ? \WeakReference::create($parent) : null;
    }

    public function getParent(): ?Node
    {
        return $this->parent?->get();
    }

    public function setFirstChild(?Node $firstChild): void
    {
        $this->firstChild = $firstChild;
    }

    public function getFirstChild(): ?Node
    {
        return $this->firstChild;
    }

    public function setLastChild(?Node $lastChild): void
    {
        $this->lastChild = $lastChild ? \WeakReference::create($lastChild) : null;
    }

    public function getLastChild(): ?Node
    {
        return $this->lastChild?->get();
    }

    public function setPreviousSibling(?Node $previousSibling): void
    {
        $this->previousSibling = $previousSibling ? \WeakReference::create($previousSibling) : null;
    }

    public function getPreviousSibling(): ?Node
    {
        return $this->previousSibling?->get();
    }

    public function setNextSibling(?Node $nextSibling): void
    {
        $this->nextSibling = $nextSibling;
    }

    public function getNextSibling(): ?Node
    {
        return $this->nextSibling;
    }

    public function getElement(): ?Element
    {
        return $this->element;
    }

    public function getElementKind(): ?ElementKind
    {
        return $this->element?->getKind();
    }

    public function getTextContent(): ?string
    {
        return $this->textContent;
    }

    public function setTextContent(string $textContent): void
    {
        if ($this->kind !== NodeKind::Text) {
            throw new \InvalidArgumentException('Text content can only be set on Text nodes');
        }
        $this->textContent = $textContent;
    }

    /**
     * 子ノードを追加
     */
    public function appendChild(Node $child): void
    {
        $child->setParent($this);

        if ($this->firstChild === null) {
            $this->firstChild = $child;
            $this->setLastChild($child);
        } else {
            $lastChild = $this->getLastChild();
            if ($lastChild !== null) {
                $lastChild->setNextSibling($child);
                $child->setPreviousSibling($lastChild);
            }
            $this->setLastChild($child);
        }
    }

    /**
     * 子ノードを削除
     */
    public function removeChild(Node $child): void
    {
        if ($child->getParent() !== $this) {
            throw new \InvalidArgumentException('Node is not a child of this node');
        }

        $previous = $child->getPreviousSibling();
        $next = $child->getNextSibling();

        if ($previous !== null) {
            $previous->setNextSibling($next);
        } else {
            // 最初の子だった場合
            $this->firstChild = $next;
        }

        if ($next !== null) {
            $next->setPreviousSibling($previous);
        } else {
            // 最後の子だった場合
            $this->setLastChild($previous);
        }

        $child->setParent(null);
        $child->setPreviousSibling(null);
        $child->setNextSibling(null);
    }

    /**
     * ノードの等価性をチェック
     */
    public function equals(Node $other): bool
    {
        if ($this->kind !== $other->kind) {
            return false;
        }

        return match ($this->kind) {
            NodeKind::Document => true,
            NodeKind::Element => $this->element?->getKind() === $other->element?->getKind(),
            NodeKind::Text => $this->textContent === $other->textContent,
        };
    }
}
