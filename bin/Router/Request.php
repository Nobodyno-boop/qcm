<?php

namespace Vroom\Router;

class Request
{
    private Route $route;

    /**
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }




}