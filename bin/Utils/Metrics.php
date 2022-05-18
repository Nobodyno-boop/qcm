<?php

namespace Vroom\Utils;

use Vroom\Container\Container;
use Vroom\Container\IContainer;

class Metrics implements IContainer
{

    private float $start;
    private float $end;

    public function start()
    {
        $this->start = microtime(true);
    }

    public function stop()
    {
        $this->end = microtime(true);
    }

    public function getTime(): float
    {
        if (!isset($this->start, $this->end)) {
            return -1.0;
        }

        return ($this->end - $this->start);
    }

    public static function getContainerNamespace(): string
    {
        return "_metrics";
    }

    public static function container(): static
    {
        return Container::get(self::getContainerNamespace());
    }
}