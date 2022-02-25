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

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }
}