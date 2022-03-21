<?php

namespace Vroom;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Vroom\App\AbstractApp;
use Vroom\Config\Config;
use Vroom\Controller\Controllers;
use Vroom\Orm\Model\Models;
use Vroom\Orm\Sql\Sql;
use Vroom\Router\Router;
use Vroom\Utils\Container;
use Vroom\View\View;

class Framework {
    private Config $config;
    private AbstractApp $app;

    /**
     * @param Config $config
     * @param AbstractApp $app
     */
    public function __construct(Config $config, AbstractApp $app)
    {
        $this->config = $config;
        $this->app = $app;
        $router = new Router();



        Container::set("_config", $this->config);
        Container::set("_db", new Sql());
        Container::set("_router", $router);
        Container::set("_twig", View::getTwig());

        if($this->getConfig()->getConfig()['debug']['checkModels']){
            foreach ($app->models() as $model){
                Models::readModel($model);
            }
        }


        $controllers = $app->controller();

        foreach ($controllers as $controller){
            Controllers::read($controller);
            $data = Controllers::get($controller);
            if(!empty($data)){
                foreach ($data['routes'] as $route){
                    $router->addRoute($route, $controller);
                }
            }

        }

        $router->handle();
    }


    public static function newInstance(string $configPath, AbstractApp $app): Framework
    {
        $include = include($configPath);
        if(is_array($include)){
            return new Framework(new Config($include), $app);
        } else {
            throw new \Error();
        }
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }


    public static function getRouter(): Router
    {
        return Container::get(Router::CONTAINER_NAMESPACE);
    }


}