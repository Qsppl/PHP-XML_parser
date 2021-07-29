<?php
// в php есть встроенный парсер expat. Я думаю будет приемлимо написать 
// интерфейс "XML" который используя expat создает удобную структуру данных на основе xml документа  

// при парсинге xml документа все узлы "XmlElement"[1] будут интерпретированы как объекты класса XML
// а все свойства этих узлов будут записаны как свойства соответствующих им объектов класса XML 
// [1] https://docs.microsoft.com/ru-ru/dotnet/standard/data/xml/types-of-xml-nodes

// модель структуры DOM[2] для удобства будет записана
// в сами xml-элементы в виде ссылки на родителя и массива с потомками
// [2] https://docs.microsoft.com/ru-ru/dotnet/standard/data/xml/process-xml-data-using-the-dom-model

spl_autoload_register(function ($class) {
    
    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/src/';
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

use Modules\XmlElement;

$document = file_get_contents('src/Data/import___bc236459-b687-474a-8555-427a5c7d44da.xml');
$xmlstructure = new XmlElement($document);
print($xmlstructure->КоммерческаяИнформация->Каталог->Товары->Товар[0]->Ид); print("\n");

print($xmlstructure->КоммерческаяИнформация->Каталог['СодержитТолькоИзменения']); print("\n");

$catalog = $xmlstructure->КоммерческаяИнформация->Каталог;
if ($catalog['СодержитТолькоИзменения'] == True) {
    foreach( $catalog->Товары->Товар as $product) {
        print($product->Наименование); print("\n");
    }
}
