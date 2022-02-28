<?php

namespace Vroom;

use Vroom\App\AbstractApp;
use Vroom\Config\Config;
use Vroom\Controller\Controllers;
use Vroom\Orm\Model\Models;
use Vroom\Orm\Sql\Sql;
use Vroom\Router\Router;
use Vroom\Utils\Container;

class Framework {
    private Config $config;
    private AbstractApp $app;

    /**
     * @param Config $config
     */
    public function __construct(Config $config, AbstractApp $app)
    {
        $this->config = $config;
        $this->app = $app;
        $router = new Router();


        Container::set("_config", $this->config);
        Container::set("_db", new Sql());
        Container::set("_router", $router);

        if($this->getConfig()->getConfig()['debug']['checkModels']){
            foreach ($app->models() as $model){
                Models::readModel($model);

            }
        }


        $controllers = $app->controller();

        foreach ($controllers as $controller){
            Controllers::read($controller);

            dump(Controllers::get($controller));

        }


        $router->get("/posts", "login");
        $router->get("/posts/:id", "loginlogu");
        $router->get("/posts/:id/:slug", "loginlogu");

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