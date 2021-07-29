<?php

namespace Modules;
use Countable, ArrayAccess, IteratorAggregate, Exception;

class XmlElement implements Countable, ArrayAccess, IteratorAggregate{

    private $pointer; // используется для построения dom во время парсинга

    private $tagName;

    private $attributes = array();

    private $cdata;

    private $parent; // ссылка на родителя

    private $childs = array(); // массив с ссылками на потомков

    private $iterPos;


    // конструктор он и есть конструктор //

    public function __construct($data){
        if( !((is_string($data)) or (is_array($data))) ){throw new Exception('TypeError');}; // перегрузки нет, объявления типов аргументов тоже. пишу костыль.

        if (is_array($data)){ // [2] от парсера получен элемент. new XML_Element(array($tag, $attributes)) <<< array $data
            $this->tagName = $data[0];
            $this->attributes = $data[1];
        } elseif (is_string($data)){ // [1] инициализация парсинга документа. new XML_Element($document) <<< string $data
            $this->parse($data);
        }
    }


    // простые методы класса //

    public function appendChild(self $element){
        $this -> childs[$element->tagName][] = &$element;
        $element->parent = &$this;
        return true;
    }


    // перегрузка вызова private/несуществующих свойств  //

    public function __get(string $tagName){ // получать элементы будем через оператор '->' <<< $document->$element (p.s. я слишком поздно понял что так нельзя делать, это может заблокировать доступ к приватным свойствам)
        if(!(isset($this->childs[$tagName]))) {throw new Exception("\"{$tagName}\" Element not found");}

        if (count($this->childs[$tagName]) == 1) { 
            if ($this->childs[$tagName][0] instanceof self) {
                return $this->childs[$tagName][0];  // если потомок с таким $tagName всего один- вернуть его
            } else {throw new Exception('typeError');}

        } else {
            if (is_array($this->childs[$tagName])){
                return $this->childs[$tagName]; // иначе вернуть весь массив с потомками
            } else {throw new Exception('typeError');}

        }
    }


    // служебный метод для получения объекта как строки. через него будем получать data //

    public function __toString():string { // если мы обращаемся к элементу как к строке, он возвращает data <<< echo $element
        return $this-> cdata;
    }


    // интерфейсы //
    
    //ArrayAccess - если мы обращаемся к элементу как к массиву, он возвращает атрибуты
    public function offsetExists($offset){ return isset($this->attributes[$offset]); } // [666] Данный метод выполняется при использовании isset() или empty() на объектах, реализующих интерфейс ArrayAccess.
    public function offsetGet($offset){
        if(!(isset($this[$offset])) ){ // [666] такие условия писать не стоит
            throw new Exception("\"{$offset}\" Attribute not found");
        }
        return $this->attributes[$offset];
    }
    public function offsetSet($offset, $value): void {
        $this->attributes[$offset] = $value;
    }
    public function offsetUnset($offset): void {
        unset($this->attributes[$offset]);
    }
    
    // Countable - __get() перегружен, возвращает array или XMLObject. 
        // если попробовать посчитать XMLObject, получится что-то не то, так что перегружаем count()
    public function count(): int {
        if ($this->parent == null) { return 1; } // если мы попытались посчитать корневой элемент, возвращаем 1
        
        return count($this->parent->childs[$this->tagName]);
    }

    //IteratorAggregate - при обращении к объекту как к иттерируемому <<< foreach $document->$ul->li
        // [010] мы хотим итерировать не сам объект, а всех потомков родителя этого объекта. __get() перегружен, возвращает array или XMLObject
    public function getIterator(): array {
        if ($this->parent == null) { return array($this); } // если мы попытались итерировать корневой элемент, возвращаем массив с ним
        
        return $this->parent->childs[$this->tagName]; // [010]
    }


    // парсер //

    private function parse(string $data){
        // настройки парсера
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, "tagOpen", "tagClose");
        xml_set_character_data_handler($parser, "cData");
        
        $this->pointer = &$this;

        // запускаем
        xml_parse($parser, $data);
        xml_parser_free($parser);
        unset($parser);
    }
    private function tagOpen($parser, string $tagName, array $attributes){
        $newXmlObject = new XmlElement(array($tagName, $attributes));
        
        $this->pointer->appendChild($newXmlObject);

        $this->pointer = &$newXmlObject;
    }
    private function tagClose($parser, string $tagName){
        $this->pointer = $this->pointer->parent;
    }
    private function cData($parser, string $cdata){
        if((boolean) trim($cdata)){
            if (isset($this->pointer->cdata)){
                $this->pointer->cdata = $this->pointer->cdata . $cdata;
            } else {
                $this->pointer->cdata = $cdata;
            }
        }
    }
}