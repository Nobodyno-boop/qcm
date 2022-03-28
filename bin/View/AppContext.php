<?php

namespace Vroom\View;
use Vroom\Utils\Container;

/**
 * Just a class used in the twig context
 */
class AppContext
{
    public array $session = [];
    public bool $debug = false;
    public $load;
    public array $other;
    /**
     * @param array $session
     * @param bool $debug
     */
    public function __construct(array $session, bool $debug, $other = [])
    {
        $loadOb = Container::get("_telemetry_time");
        $loadOb->stop();
        $this->load = $loadOb->getTime();

        $this->session = $session;
        $this->debug = $debug;
        $this->other = $other;
    }


}