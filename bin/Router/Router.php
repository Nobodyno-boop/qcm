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


    public function addRoute(array $data, $controller)
    {
        foreach ($data['methods'] as $method){
            $route = new Route($data, $controller, $method);
            $this->routes[$method][] = $route;
        }
    }

    public function handle()
    {
       $url = $_GET['url'];
        $r = null;
        $routes = $this->routes[$_SERVER['REQUEST_METHOD']] ?? [];
        if(!empty($routes)){
            usort($routes, function ($a, $b){
                if($a == $b) return 0;
                return (strlen($b->getUrl()) > strlen($a->getUrl()) ? -1 : 1);
            });

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
        } else {
            // No route so 404
        }

    }


    private function callController(Route $route)
    {
        try {
            $class = new \ReflectionClass($route->getController());
            $request = new Request($route);
            $obj = $class->newInstance($request);
            $method = $class->getMethod($route->getMethod());
            $params = [];
            if($method->getNumberOfRequiredParameters() >= 1){
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter){
                    switch ($parameter->getType()){
                        case Request::class:

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