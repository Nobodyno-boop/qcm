<?php

namespace Vroom\App;

abstract class AbstractApp
{
    public abstract function controller(): array;

    public abstract function models(): array;


}