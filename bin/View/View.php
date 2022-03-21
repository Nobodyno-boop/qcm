<?php

namespace Vroom\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Vroom\Utils\Container;

class View
{
    private static Environment $twig;

    /**
     * @return Environment
     */
    public static function getTwig(): Environment
    {

        return self::$twig ?? self::make();
    }

    private static function make(): Environment
    {
        $config = Container::get("_config")->getConfig();
        $loader = new FilesystemLoader($config['template']['dir']);
        $twig = new Environment($loader, ['debug' => true]);
        $url = $config['site'];
        $asset = new TwigFunction('asset', function ($path) use ($url) {
            return $url['url'] . $url['assets'] . "/$path";
        });


        $twig->addFunction($asset);
        self::$twig = $twig;
        return $twig;
    }
}