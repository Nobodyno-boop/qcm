<?php

namespace Vroom;

use Spatie\Ignition\Ignition;
use Vroom\App\AbstractApp;
use Vroom\Config\Config;
use Vroom\Container\Container;
use Vroom\Controller\Controllers;
use Vroom\Orm\Model\Models;
use Vroom\Orm\Sql\Sql;
use Vroom\Router\Router;
use Vroom\Utils\Metrics;
use Vroom\View\View;

class Framework
{
    private Config $config;
    private AbstractApp $app;

    /**
     * @param Config $config
     * @param AbstractApp $app
     */
    public function __construct(Config $config, AbstractApp $app)
    {
        // Session
        session_name("qcm_id"); // change default cookie name
        session_start();
        Ignition::make()->register();


        $renderPageTime = new Metrics();
        $renderPageTime->start();
        Container::setObject($renderPageTime);
        $this->config = $config;
        $this->app = $app;
        $router = new Router();

        Container::setObject($this->config);
        Container::setObject(new Sql());
        Container::setObject($router);
        Container::set("_twig", View::getTwig());

        foreach ($app->models() as $model) {
            Models::readModel($model);
        }

        $controllers = $app->controller();

        foreach ($controllers as $controller) {
            Controllers::read($controller);
            $data = Controllers::get($controller);
            if (!empty($data)) {
                foreach ($data['routes'] as $route) {
                    $router->addRoute($route, $controller);
                }
            }
        }
        $router->handle();
    }


    public static function newInstance(string $configPath, AbstractApp $app): Framework
    {
        $include = include($configPath);
        if (is_array($include)) {
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
        return Router::container();
    }


}