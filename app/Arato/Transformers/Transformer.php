<?php

namespace Arato\Transformers;

use Underscore\Types\Arrays;


abstract class Transformer
{
    public function transformCollection(array $items)
    {
        return Arrays::invoke($items, [$this, 'transform']);
    }

    public abstract function transform($item);
}