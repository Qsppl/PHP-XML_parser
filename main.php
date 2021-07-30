<?php

use Modules\XmlParser\ExampInterpreter;

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/src/'; // base directory for the namespace prefix

    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) { // if the file exists, require it
        require $file;
    }

});


$document = file_get_contents('src/Data/testfile.xml');

$interpriter = new ExampInterpreter($document);

$structure = $interpriter->rootNode;

var_dump($structure);

print("\n");

print($xmlstructure->КоммерческаяИнформация->Каталог['СодержитТолькоИзменения']);

print("\n");

$catalog = $xmlstructure->КоммерческаяИнформация->Каталог;

if ($catalog['СодержитТолькоИзменения'] == true) {
    foreach ($catalog->Товары->Товар as $product) {
        print($product->Наименование);

        print("\n");
    }

}
