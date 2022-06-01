<?php

namespace Vroom\Config;

use Vroom\Container\Container;
use Vroom\Container\IContainer;
use Vroom\Utils\ArrayUtils;

class Config extends ArrayUtils implements IContainer
{

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }


    public static function getContainerNamespace(): string
    {
        return "_config";
    }

    public static function container(): static
    {
        return Container::get(self::getContainerNamespace());
    }
}