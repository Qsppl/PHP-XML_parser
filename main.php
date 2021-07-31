<?php

require 'src/autoload.php';

use src\XmlElement;

$document = file_get_contents('Data/import___bc236459-b687-474a-8555-427a5c7d44da.xml');

$xmlstructure = new XmlElement($document);

print($xmlstructure);

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
