<?php

require 'src/autoload.php';

use src\Product;
use src\XmlElement;

$document = file_get_contents('Data/import___bc236459-b687-474a-8555-427a5c7d44da.xml');
$xmlstructure = new XmlElement($document);
unset($document);

$catalog = $xmlstructure->КоммерческаяИнформация->Каталог;
$products = array();
foreach ($catalog->Товары->Товар as $product) {
    $products[] = new Product($product);
}

foreach ($products as $product) {
    print($product->name."\n");
}
