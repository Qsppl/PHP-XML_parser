<?php

namespace Modules\XmlStructure;

use Modules\DomLikeNavigable\DomLikeNavigable;
use Modules\XmlParser\StepByStepConstruct;
use Modules\XmlNode\XmlNode;
use Exception;


abstract class XmlStructure implements StepByStepConstruct, DomLikeNavigable
{
    protected static $ROOT_NAMES_FOR_STRUCTURES = [
        '' => 'XmlStrustureDocument',
    ];

    public $typeOfStructure;

    protected $rootNode;

    protected $childsStructure = array();

    protected $pointer;

    public function __construct(XmlNode $rootNode) {
        $this->rootNode = &$rootNode;

        $this->pointer = &$this->rootNode;
    }

    public function createStructureByRoot(XmlNode $rootNode): XmlStructure
    {
        $className = $rootNode->name;
        $newStructure = new $className();

        $this->childsStructure[$newStructure->name][] = &$newStructure;
        $newStructure->parentStructure = &$this;

        return $newStructure;
    }

    final public static function isRootForStructure(string $nodeName): bool
    {
        //if (array_key_exists($nodeName, XmlStructure::$ROOT_NAMES_FOR_STRUCTURES)) {
        //    return true;
        //}
        return false;
    }

    ### DomLikeNavigable ###

    public function __get(string $typeOfStructure)
    {
        if (count($this->childsStructure[$typeOfStructure]) == 1) {
            if ($this->childsStructure[$typeOfStructure][0] instanceof self) {
                return $this->childsStructure[$typeOfStructure][0];  // если потомок с таким $name всего один- вернуть его
            } else {
                throw new Exception('typeError');
            }
        } else {
            if (is_array($this->childsStructure[$typeOfStructure])) {
                return $this->childsStructure[$typeOfStructure]; // иначе вернуть весь массив с потомками
            } else {
                throw new Exception('typeError');
            }
        }
    }

    # ArrayAccess - если мы обращаемся к элементу как к массиву, он возвращает атрибуты
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    # Данный метод выполняется при использовании isset() или empty() на объектах, реализующих интерфейс ArrayAccess.
    public function offsetGet($offset)
    {
        if (!isset($this[$offset])) {
            throw new Exception("\"{$offset}\" Attribute not found");
        }
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    # Countable - __get() перегружен, возвращает array или XMLObject,
    // но если попробовать посчитать XMLObject, получится что-то не то, так что перегружаем count()
    public function count(): int
    {
        if ($this->parent == null) {
            return 1; // если мы попытались посчитать количество рутов документа, возвращаем 1
        }
        return count($this->parent->childs[$this->name]);
    }

    #IteratorAggregate - при обращении к объекту как к иттерируемому <<< foreach $document->$ul->li
    // __get() перегружен, возвращает array или XMLObject; [010] мы хотим итерировать не сам объект, а все объекты с таким именем.
    public function getIterator(): array
    {
        if ($this->parent == null) {
            return array($this);
        } // если мы попытались итерировать корневой элемент, возвращаем массив с ним
        return $this->parent->childs[$this->name]; // [010]
    }
}
