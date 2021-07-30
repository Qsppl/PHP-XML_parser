<?php

// Интерпретатор получает от examp-токенизатора группы токенов: tagopen, cdata и tagclose.
// Приняв tagopen создаёт экземпляр XmlNode и позже отправляет этому экземпляру токены cdata.
// Созданный экземпляр XmlNode будет отправлен верхнему конструктору в стаке.
// При получении от examp токена tagclose Интерпретатор сообщает об этом верхнему конструктору в стаке.

// При инициализации Интерпретатор создаёт конструктор XmlStructureDocument и кладет его в стак

// Стак xml-конструкторов (XmlStructure реализующие интерфейс StepByStepConstruct).
// Интерпретатор всегда работает с верхним конструктором в стаке.
// По мере обработки xmlDocument могут встречаться новые XmlStructure- они добавляются на верх стака.
// Интерпретатор сам определяет когда закончить построение XmlStructure и удаляет её из стака.

namespace Modules\XmlParser;

use Modules\XmlNode\XmlNode;
use Modules\XmlStructure\{XmlStructure, XmlStructureDocument};

class ExampInterpreter implements StepByStepInterpriter
{
    private $stackOfRecipients = array();

    private $cDataBoofer;

    private $currentNode;

    public $rootNode;

    public function __construct(string $xmlDocument, bool $CaseFolding = false)
    {
        $rootNode = new XmlNode();
        $rootStructure = new XmlStructureDocument($rootNode);

        $this->stackOfRecipients[] = &$rootStructure;
        $this->currentNode = $rootNode;
        $this->rootNode = $rootNode;

        $this->parse($xmlDocument, $CaseFolding);
    }

    private function getTopStackItem(): XmlStructure
    {
        return $this->stackOfRecipients[count($this->stackOfRecipients) - 1];
    }

    private function sendNodeToStructure(XmlNode $node): void
    {
        $this->getTopStackItem()->addXmlNodeToPoint($node);
    }

    private function sendCdata(string $cData): void
    {
        if (isset($cData))
        $this->currentNode->setCData($cData);
    }

    private function checkStructureEntry(string $nodeName): bool
    {
        if ($this->getTopStackItem()->typeOfStructure == $nodeName) {
            return false;
        } else {
            return true;
        }
    }

    // examp

    private function parse(string $data, bool $CaseFolding)
    {
        $parser = xml_parser_create();

        // настройки парсера

        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, $CaseFolding);

        xml_set_object($parser, $this);

        xml_set_element_handler($parser, "tagOpen", "tagClose");

        xml_set_character_data_handler($parser, "cData");

        // запускаем

        xml_parse($parser, $data);

        // освобождаем память

        xml_parser_free($parser);

        unset($parser);
    }

    public function tagOpen($parser, string $nodeName, array $attributes)
    {
        $node = new XmlNode($nodeName, $attributes);

        if (XmlStructure::isRootForStructure($node)) {
            $this->currentNode->appendChild($node);

            $newStructure = &$this->getTopStackItem()->createStructureByRoot($node);

            $this->stackOfRecipients[] = $newStructure;
        } else {
            $this->sendNodeToStructure($node);

            $this->getTopStackItem()->addXmlNodeToPoint($this->currentNode);
        }
        $this->currentNode = &$node;
    }

    public function cData($parser, string $cData)
    {
        if ((bool) trim($cData)) { // если $cData не пустая
            if (isset($this->cDataBoofer)) {
                $this->cDataBoofer = $this->cDataBoofer . $cData;
            } else {
                $this->cDataBoofer = $cData;
            }
        }
    }

    public function tagClose($parser, string $nodeName)
    {
        if (isset($this->cDataBoofer)) {
            $this->sendCdata($this->cDataBoofer);
            $this->cDataBoofer = null;
        }

        if ($this->checkStructureEntry($nodeName)) {
            return;
        } else {
            $this->getTopStackItem()->leaveCurrentXmlNode();
        }
    }
}
