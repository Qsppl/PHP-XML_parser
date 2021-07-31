<?php

# Я предположил что все элементы структуры данных "Товар" и их взаимное расположение
# заранее определены в БД и не будет меняться, поэтому писать универсальное решение не требуется.

# так же я не знаю как предпологалось использовать данный класс:
#   1. для того чтобы отредактировать xml-документ и далее продолжить работать с ним
#   (сохранив изначальную структуру xml-документа и лишь редактируя его узлы и добавляя новые)
#   2. для того чтобы получить целевые данные из xml-документа и далее работать с ними
#   (не сохраняя структуру xml-документа; записать лишь данные о товарах без привязки к dom)
# поэтому реализую простой второй вариант. первый вариант попытаюсь реализовать в ветке testing

namespace src;

use FFI\Exception;

class Product
{
    public $id;
    public $versionNumber;
    public $deleteMark;
    public $barcode ;
    public $vendorCode;
    public $name;
    public $baseUnit;
    public $groups = array();
    public $description;
    public $picture;
    public $country;
    public $propertyValues = array();
    public $AttributeValues = array();

    public function __construct(XmlElement $node) 
    {
        $this->getData($node);
    }
    private function getData(XmlElement $node) 
    {
        $this->id = (string) $node->Ид;
        $this->versionNumber = (string) $node->НомерВерсии;
        $this->deleteMark = (bool) $node->ПометкаУдаления;
        $this->barcode = (string) $node->Штрихкод;
        $this->vendorCode = (string) $node->Артикул;
        $this->name = (string) $node->Наименование;
        $this->baseUnit = (string) $node->БазоваяЕдиница;
        $this->groups = array(); // массив значений
        $this->description = (string) $node->Описание;
        $this->pictures = array(); // массив значений
        $this->country = (string) $node->Страна;
        $this->propertyValues = array(); // ассоциативный массив
        $this->requisitesValues = array(); // массив ассоциативных массивов

        foreach ($node->Группы->Ид as $id) {
            $this->groups[] = (string) $id;
        }

        foreach ($node->Картинка as $picture) {
            $this->pictures[] = (string) $picture;
        }

        foreach ($node->ЗначенияСвойств->ЗначенияСвойства as $propertyValue) {
            $propertyId = (string) $propertyValue->Ид;
            $propertyValue = (string) $propertyValue->Значение;
            $this->propertyValues += [$propertyId, $propertyValue];
        }

        foreach ($node->ЗначенияРеквизитов->ЗначениеРеквизита as $requisiteValues) {
            $name = (string) $requisiteValues->Наименование;
            $value = (string) $requisiteValues->Значение;
            $this->requisitesValues[] = [$name, $value];
        }
    }
}