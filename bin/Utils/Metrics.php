<?php

namespace Vroom\Utils;

class Metrics
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

}