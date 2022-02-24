<?php

namespace Vroom\Config;

class Config {
    private array $config;
    /**
     * @var mixed|null
     */

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }


    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }

}