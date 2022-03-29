<?php

namespace Vroom\Config;

use Vroom\Utils\ArrayUtils;

class Config
{
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
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
     * @param string $path
     * @return mixed
     */
    public function get(string $path): mixed
    {
        return ArrayUtils::from($this->config)->get($path);
    }
}