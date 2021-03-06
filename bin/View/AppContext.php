<?php

namespace Vroom\View;

use Vroom\Container\Container;
use Vroom\Utils\Metrics;

/**
 * Just a class used in the twig context
 */
class AppContext
{
    public array $session = [];
    public array $user;
    public bool $debug = false;
    public $load;
    public array $other;

    /**
     * @param array $session
     * @param bool $debug
     * @param array $other
     */
    public function __construct(array $session, bool $debug, array $other = [])
    {
        $loadOb = Metrics::container();
        $loadOb->stop();
        $this->load = $loadOb->getTime();

        $this->session = $session;
        $this->user = $session['user'] ?? [];
        $this->debug = $debug;
        $this->other = $other;
    }


}