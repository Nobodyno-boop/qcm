<?php

namespace Vroom\Controller;

use Vroom\Framework;
use Vroom\Router\Decorator\Route;
use Vroom\Utils\Container;

class Controllers
{
    const CONTAINER_NAMESPACE = "_controllers";

    public static function read(string|object $controller)
    {
        try{
            if(is_object($controller)){
                $controller = get_class($controller);
            }
            $class = new \ReflectionClass($controller);
            if(!$class->isSubclassOf(AbstractController::class)){
                throw new \Error("Cannot init a controller without extends AbAbstractController");
            }
            $results = [];
            foreach ($class->getMethods() as $method){
                $routes = $method->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF);
                if(!empty($routes)){
                    /**
                     * @var Route $route
                     */
                    $route = $routes[0]->newInstance();
                    $result["name"] = $method->getName();
                    $result['url'] = $route->getUrl();
                    $result['prefix'] = $route->getName();
                    $result['methods'] = $route->getMethods();
                    $results[] = $result;
                }
            }

            if(empty(self::get($controller))){
                Container::set(self::CONTAINER_NAMESPACE, [$controller=> [
                    "routes" => $results
                ]]);
            } else {
                $controllers = self::get($controller);
                $controllers = array_merge($controllers, [
                    "routes" => $results
                ]);
                Container::set(self::CONTAINER_NAMESPACE, $controllers);
            }
        }catch (\Exception $e){
            die($e);
        }
    }


    public static function get(string $name)
    {
        $controllers = Container::get(self::CONTAINER_NAMESPACE);
        return $controllers[$name] ?? [];
    }
}