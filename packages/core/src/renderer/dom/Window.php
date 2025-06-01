<?php

declare(strict_types=1);

namespace MyApp\Core\Renderer\Dom;

/**
 * DOMのWindowオブジェクト
 */
class Window
{
    private Node $document;

    public function __construct()
    {
        $this->document = new Node(NodeKind::Document);
        $this->document->setWindow($this);
    }

    public function getDocument(): Node
    {
        return $this->document;
    }
}
