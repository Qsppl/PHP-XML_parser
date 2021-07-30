<?php

namespace Modules\XmlStructure;

use FFI\Exception;
use Modules\XmlNode\XmlNode;

class XmlStructureDocument extends XmlStructure
{
    public function __construct(XmlNode $rootNode) {
        $this->typeOfStructure = 'Document';
        $this->rootNode = &$rootNode;

        $this->pointer = &$this->rootNode;

        $rootNode->setXmlStructure($this);
    }

    public function addXmlNodeToPoint(XmlNode $node): void
    {
        $this->pointer->appendChild($node);

        $this->pointer = &$node;
    }

    public function leaveCurrentXmlNode(): void
    {
        $this->pointer = $this->pointer->parent;
    }
}
