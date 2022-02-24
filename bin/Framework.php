<?php

namespace Vroom;

use Vroom\Config\Config;

class Framework {
    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;

    }


    public static function newInstance(string $configPath): Framework
    {
        $include = include($configPath);
        if(is_array($include)){
            return new Framework(new Config($include));
        } else {
            throw new \Error();
        }
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}