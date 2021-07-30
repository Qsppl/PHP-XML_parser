<?php

namespace Modules\DomLikeNavigable;

interface DomLikeNavigable extends \Countable, \ArrayAccess, \IteratorAggregate
{
    public function __get(string $name);
}
