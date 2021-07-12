<?php
// написать класс с реализацией методов по парсингу xml-файла с товарами 
// в структуру данных любого вида - массив, массив объектов

// в php есть встроенный парсер expat. Я думаю будет приемлимо написать 
// интерфейс "XML" который используя expat создает удобную структуру данных на основе xml документа  

// при парсинге xml документа все узлы "XmlElement"[1] будут интерпретированы как объекты класса XML
// а все свойства этих узлов будут записаны как свойства соответствующих им объектов класса XML 
// [1] https://docs.microsoft.com/ru-ru/dotnet/standard/data/xml/types-of-xml-nodes

class XML // каждый экземпляр класса представляет собой один XmlElement, 
{
    // СВОЙСТВА XML ЭЛЕМЕНТА
    private $pointer;

    private $tagName;

    private $attributes = array();

    private $cdata;

    private $parent;

    private $childs = array();

    // переписываем служебные методы чтобы можно было нормально работать с объектом
    public function __construct($data)
    {
        if (is_array($data)){ // объект получит либо 
            
        }
    }

    public function __toString()
    {
        return;
    }

    public function __get($name)
    {
        return;
    }
}
