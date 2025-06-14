<?php

declare(strict_types=1);

namespace Yasuaki640\PhpRenderingEngine\Core\Tests;

use PHPUnit\Framework\TestCase;
use Yasuaki640\PhpRenderingEngine\Core\renderer\dom\Element;
use Yasuaki640\PhpRenderingEngine\Core\renderer\dom\Node;
use Yasuaki640\PhpRenderingEngine\Core\renderer\dom\NodeKind;
use Yasuaki640\PhpRenderingEngine\Core\Utils;

class UtilsTest extends TestCase
{
    public function testConvertDomToStringWithNullRoot(): void
    {
        $result = Utils::convertDomToString(null);
        $this->assertEquals("\n", $result);
    }

    public function testConvertDomToStringWithSingleNode(): void
    {
        $node = new Node(NodeKind::Text, 'Hello');
        $result = Utils::convertDomToString($node);

        $expected = "\nText\n";
        $this->assertEquals($expected, $result);
    }

    public function testConvertDomToStringWithNestedNodes(): void
    {
        // Create a simple DOM structure
        // <html>
        //   <body>
        //     Hello
        //   </body>
        // </html>

        $htmlElement = new Element('html');
        $htmlNode = new Node(NodeKind::Element, $htmlElement);

        $bodyElement = new Element('body');
        $bodyNode = new Node(NodeKind::Element, $bodyElement);

        $textNode = new Node(NodeKind::Text, 'Hello');

        // Build the tree
        $htmlNode->appendChild($bodyNode);
        $bodyNode->appendChild($textNode);

        $result = Utils::convertDomToString($htmlNode);

        $expected = "\nElement\n  Element\n    Text\n";
        $this->assertEquals($expected, $result);
    }

    public function testConvertDomToStringWithSiblings(): void
    {
        // Create a DOM structure with siblings
        // <div>First</div><div>Second</div>

        $firstDiv = new Element('div');
        $firstDivNode = new Node(NodeKind::Element, $firstDiv);
        $firstTextNode = new Node(NodeKind::Text, 'First');
        $firstDivNode->appendChild($firstTextNode);

        $secondDiv = new Element('div');
        $secondDivNode = new Node(NodeKind::Element, $secondDiv);
        $secondTextNode = new Node(NodeKind::Text, 'Second');
        $secondDivNode->appendChild($secondTextNode);

        // Add second div as sibling of first
        $firstDivNode->setNextSibling($secondDivNode);

        $result = Utils::convertDomToString($firstDivNode);

        $expected = "\nElement\n  Text\nElement\n  Text\n";
        $this->assertEquals($expected, $result);
    }
}
