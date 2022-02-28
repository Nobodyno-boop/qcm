<?php

namespace Vroom\Router;


/**
 *  Router take always the last better
 */
class Router
{
    /**
     * @var Route[] $routes
     */
    private array $routes;
    const CONTAINER_NAMESPACE = "_router";

    public function get(string $url, string $controller): Route
    {
        return $this->addRoute($url, $controller, "GET");
    }

    public function addRoute(string $url, string $controller, string $method): Route
    {
        $route = new Route($url, $controller);
        $this->routes[$method][] = $route;
        return $route;
    }

    public function handle()
    {
       $url = $_GET['url'];
        $r = null;
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if($route->match($url)){
                $r = $route;
            }
        }


        if($r != null){

        } else {
//            throw new \Error("Cannot find route");
        }
    }




}