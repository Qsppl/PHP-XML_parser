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

// чтение файла из потока и обработку файла сегментами скорее всего не нужно реализовывать для тестового задания
// так же в тз не указано что нужно реализовать методы записи данных обратно в xml

class XML // каждый экземпляр класса представляет собой один XmlElement, 
{
    // СВОЙСТВА XML ЭЛЕМЕНТА
    private $pointer; // используется для построения dom во время парсинга

    private $tagName;

    private $attributes = array();

    private $cdata;

    private $parent; // ссылка на родителя

    private $childs = array(); // массив с ссылками на потомков

    private $iterPos;


    public function __construct($data){
        if( !((is_string($data)) or (is_array($data))) ){throw new Exception('TypeError');}; // перегрузки нет, объявления типов аргументов тоже. пишу костыль.

        if (is_array($data)){ // [2] от парсера получен элемент. new XML(array($tag, $attributes)) <<< array $data
            $this->tagName = $data[0];
            $this->attributes = $data[1];
        } elseif (is_string($data)){ // [1] инициализация парсинга документа. new XML($document) <<< string $data
            $this->parse($data);
        }
    }

    public function appendChild(self $element){
        $this -> childs[$element->tagName][] = &$element;
        $element->parent = &$this;
        return true;
    }

    // интерфейсы
    

    // парсер
    private function parse(string $data){
        // настройки парсера
        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, "tag_open", "tag_close");
        xml_set_character_data_handler($parser, "cdata");
        
        $this->pointer = &$this;

        // запускаем
        xml_parse($parser, $data);
        xml_parser_free($parser);
        unset($parser);
    }
    private function tag_open($parser, string $tagName, array $attributes){
        $newXmlObject = new XML(array($tagName, $attributes));
        
        $this->pointer->appendChild($newXmlObject);

        $this->pointer = &$newXmlObject;
    }
    private function tag_close($parser, string $tagName){
        $this->pointer = $this->pointer->parent;
    }
    private function cdata($parser, string $cdata){
        $this->pointer->cdata = $cdata;
    }
}

$document = file_get_contents('source/testfile.xml');
$xmlstructure = new XML("<ЗначенияСвойства>
<Ид>40a3098f-ed8f-11e8-af65-c6a3bfc032f2</Ид>
<Значение>229f20f6-543e-11e9-8458-485b3977ac2a</Значение>
</ЗначенияСвойства>");
var_dump($xmlstructure);