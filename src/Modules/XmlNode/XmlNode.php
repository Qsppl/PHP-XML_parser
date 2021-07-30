<?php

namespace Modules\XmlNode;

use Modules\DomLikeNavigable\DomLikeNavigable;
use Exception;
use Modules\XmlStructure\XmlStructure;

class XmlNode implements DomLikeNavigable
{
    private $name;

    private $attributes = array();

    private $cData;

    private $parent; // ссылка на родителя

    private $childs = array(); // массив с ссылками на потомков

    private $xmlStructure;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setXmlStructure(XmlStructure $structure): void
    {
        $this->xmlStructure = &$structure;
    }

    public function __construct(string $name = '', array $attributes = [])
    {
            $this->name = $name;

            $this->attributes = $attributes;
    }

    public function setCData(string $newCData): void
    {
        $this->cData = $newCData;
    }

    public function appendChild(XmlNode $xmlNode): void
    {
        $this->childs[$xmlNode->name][] = &$xmlNode;

        $xmlNode->parent = &$this;
    }

    ### DomLikeNavigable ###

    public function __get(string $name)
    {
        if (count($this->childs[$name]) == 1) {
            if ($this->childs[$name][0] instanceof XmlNode) {
                return $this->childs[$name][0];  // если потомок с таким $name всего один- вернуть его
            } else {
                throw new Exception('typeError');
            }
        } else {
            if (is_array($this->childs[$name])) {
                return $this->childs[$name]; // иначе вернуть весь массив с потомками
            } else {
                var_dump($name);
                throw new Exception('typeError');
            }
        }
    }

    public function __toString(): string
    {
        if (isset($cData)) {
            return $this->cData;
        } else {
            return '';
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
