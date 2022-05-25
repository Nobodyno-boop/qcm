<?php

namespace Vroom\Router;

use Vroom\Container\Container;
use Vroom\Container\IContainer;

/**
 *  Router take always the last better
 */
class Router implements IContainer
{
    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    public function addRoute(array $data, $controller)
    {
        foreach ($data['methods'] as $method) {
            $route = new Route($data, $controller, $method);
            $this->routes[$method][] = $route;
        }
    }

    public function handle()
    {
        $url = $_GET['url'] ?? $_SERVER['REQUEST_URI'];
        $r = null;
        $routes = $this->routes[$_SERVER['REQUEST_METHOD']] ?? [];
        if (!empty($routes)) {
            usort($routes, function ($a, $b) {
                if ($a == $b) return 0;
                return (strlen($b->getPath()) > strlen($a->getPath()) ? -1 : 1);
            });

            foreach ($routes as $route) {
                if (!$route->match($url, $_SERVER['REQUEST_METHOD'])) {
                    continue;
                }

                $r = $route;
            }
            if ($r != null) {
                Container::set("currentRoute", $r->getPath());
                $this->callController($r);
            } else {
                http_response_code(404);
                $page = $this->find404();
                if (!$page) {
                    throw new \Error("Cannot find route");
                }

                $this->callController($page);
            }
        } else {
            // No route so 404
            throw new \Error("Cannot find route (Big)");

        }

    }

    private function find404(): ?Route
    {
        $page = array_filter($this->getRoutes()['GET'] ?? [], function ($route) {
            if ($route->getName() === '404') {
                return $route;
            }
        });

        return count($page) ? $page[0] : null;
    }

    private function callController(Route $route)
    {
        try {
            $class = new \ReflectionClass($route->getController());
            $request = new Request($route);
            $obj = $class->newInstance($request);
            $method = $class->getMethod($route->getControllerMethod());
            $params = [];
            if ($method->getNumberOfRequiredParameters() >= 1) {
                $parameters = $method->getParameters();
                foreach ($parameters as $parameter) {
                    switch ($parameter->getType()) {
                        case Request::class:
                            $params[] = $request;
                            break;
                        default:
                            $name = $parameter->getName();
                            $params[] = $route->getVars()[$name] ?? null;
                            break;
                    }
                }
            }
            $method->invokeArgs($obj, $params);
        } catch (\ReflectionException $e) {
            throw new \Error($e);
//            die($e);
        }
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }


    public static function getFromPrefix(string $prefix)
    {
        $routes = self::container()->getRoutes();

        foreach ($routes as $method) {
            foreach ($method as $route) {
                if ($prefix === $route->getName()) {
                    return $route;
                }
            }
        }
        return null;
    }

    public static function getContainerNamespace(): string
    {
        return "_router";
    }

    public static function container(): static
    {
        return Container::get(self::getContainerNamespace());
    }
}