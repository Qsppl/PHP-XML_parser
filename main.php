<?php

use Modules\XmlElement;

spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/src/'; // base directory for the namespace prefix

    $file = $base_dir . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) { // if the file exists, require it
        require $file;
    }

});


$document = file_get_contents('src/Data/import___bc236459-b687-474a-8555-427a5c7d44da.xml');

$xmlstructure = new XmlElement($document);

print($xmlstructure->КоммерческаяИнформация->Каталог->Товары->Товар[0]->Ид);

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
