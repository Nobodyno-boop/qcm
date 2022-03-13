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
    private array $routes = [];
    const CONTAINER_NAMESPACE = "_router";


    public function addRoute(array $data, $controller): Route
    {

        $route = new Route($data, $controller);
        foreach ($data['methods'] as $method){
            $this->routes[$method][] = $route;
        }

        return $route;
    }

    public function handle()
    {
       $url = $_GET['url'];
        $r = null;
        $routes = $this->routes[$_SERVER['REQUEST_METHOD']] ?? [];
        foreach ($routes as $route){
            if($route->match($url)){
                $r = $route;
            }
        }

        if($r != null){
            $this->callController($r);
        } else {
//            throw new \Error("Cannot find route");
        }
    }


    private function callController(Route $route)
    {
        try {
            $class = new \ReflectionClass($route->getController());
            $obj = $class->newInstance();
            $method = $class->getMethod($route->getMethod());
            $params = [];
            if($method->getNumberOfRequiredParameters() >= 1){
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter){
                    switch ($parameter->getType()){
                        case Request::class:
                                $request = new Request($route);
                                $params[] = $request;
                            break;
                        default:
                            $name = $parameter->getName();
                            $params[] = $route->getParams()[$name] ?? null;
                            break;
                    }
                }
            }
            $method->invokeArgs($obj, $params);
        } catch (\ReflectionException $e) {
        }
    }

}