<?php

namespace Vroom\View;
/**
 * Just a class used in the twig context
 */
class AppContext
{
    public array $session = [];
    public bool $debug = false;
    /**
     * @param array $session
     * @param bool $debug
     */
    public function __construct(array $session, bool $debug)
    {
        $this->session = $session;
        $this->debug = $debug;
    }


}