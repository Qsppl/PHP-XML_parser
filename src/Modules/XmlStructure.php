<?php

namespace Modules;

const TARGT_ELEMENTS = [
  'Товар' => '',
];

abstract class XmlStructure implements XmlElement
{
  private $typeOfStructure;

  private $pointer; // используется для построения dom во время парсинга
}