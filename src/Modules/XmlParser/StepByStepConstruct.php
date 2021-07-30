<?php

namespace Modules\XmlParser;

use Modules\XmlNode\XmlNode;

interface StepByStepConstruct
{
    public function addXmlNodeToPoint(XmlNode $XmlNode): void;
    public function leaveCurrentXmlNode(): void;
}