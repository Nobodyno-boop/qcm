<?php

namespace Vroom\App;

/**
 * Base class for create the application
 */
abstract class AbstractApp
{
    /**
     * Array of controller we use in the project
     * @return array
     */
    public abstract function controller(): array;

    /**
     * Array of Models we gonna use in the project
     * @return array
     */
    public abstract function models(): array;


}