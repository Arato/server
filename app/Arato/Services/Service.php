<?php

namespace Arato\Service;


abstract class Service {
    public abstract function filter(Array $filters);

    public abstract function create($item);
}