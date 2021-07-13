<?php
// написать класс с реализацией методов по парсингу xml-файла с товарами 
// в структуру данных любого вида - массив, массив объектов

// в php есть встроенный парсер expat. Я думаю будет приемлимо написать 
// интерфейс "XML" который используя expat создает удобную структуру данных на основе xml документа  

// при парсинге xml документа все узлы "XmlElement"[1] будут интерпретированы как объекты класса XML
// а все свойства этих узлов будут записаны как свойства соответствующих им объектов класса XML 
// [1] https://docs.microsoft.com/ru-ru/dotnet/standard/data/xml/types-of-xml-nodes

// модель структуры DOM[2] для удобства будет записана
// в сами xml-элементы в виде ссылки на родителя и массива с потомками

class XML // каждый экземпляр класса представляет собой один XmlElement, 
{
    // СВОЙСТВА XML ЭЛЕМЕНТА
    private $pointer;

    private $tagName;

    private $attributes = array();

    private $cdata;

    private $parent; // ссылка на родителя

    private $childs = array(); // массив с ссылками на потомков


    public function __construct($data){
        // создание нового объекта XML может происходить в двух случаях:
        // [1] кто-то создал объект XML и передал ему документ.xml new XML($document) <<< string $data
        // [2] объект уже парсит документ, встретил xml-элемент и вызвал new XML(array($tag, $attributes)) <<< array $data
        if (is_array($data)){ // [2]
            $this->tagName = $data[0];
            $this->attributes = $data[1];
        } elseif (is_string($data)){ // [1]
            $this->parse($data);
        }
    }

    public function __toString(){
        return;
    }

    public function __get($name){
        return;
    }

    public function appendChild($element){ // добавлять дочерний элемент можно двумя способами, 
                                                // передать (array($tag, $attributes)) или (&$xmlObject)
        if (is_array($element)){
            $newXmlObject = new XML($element);
            
            $tag = $element[0];
            $this -> clilds[$tag][] = &$newXmlObject;

            $newXmlObject->parent = &$this;

            return $newXmlObject;
        } elseif(is_object($element)){
            $this -> clilds[$element->tagName][] = $element;
            $element->parent = &$this;
            return true;
        };
    }

    private function parse($data){
        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_set_element_handler($this->parser, "tag_open", "tag_close");
        xml_set_character_data_handler($this->parser, "cdata");
        
        $this->pointer = &$this;
        xml_parse($parser, $data);
        xml_parser_free($parser);
        unset($parser);
    }
    private function tag_open($parser, $tag, $attributes){
        $newXmlObject = $this->pointer->appendChild($tag, $attributes);
        $this->pointer = &$newXmlObject;
    }
    private function tag_close($parser, $tagName){
        $this->pointer = $this->pointer->parent;
    }
    private function cdata($parser, $cdata){
        $this->pointer->cdata = $cdata;
    }
}