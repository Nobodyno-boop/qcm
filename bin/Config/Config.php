<?php

namespace Vroom\Config;

use Vroom\Utils\ArrayUtils;

class Config
{
    private $config;

    /**
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Return the config as array
     * @return mixed
     */
    public function getConfigArray(): mixed
    {
        return $this->config;
    }


    /**
     * Use the ArrayUtils class and get
     * Return null is the path ins't valid.
     * @param $path
     * @return mixed
     */
    public function get($path): mixed
    {
        return ArrayUtils::from($this->config)->get($path);
    }
}