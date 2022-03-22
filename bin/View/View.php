<?php

namespace Vroom\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Vroom\Security\Token;
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
        $furl = new TwigFunction('url', function ($path) use ($url){
            if(!str_starts_with($path, "/")){
                $path = "/".$path;
            }
            return $url['url'].$path;
        });
        $asset = new TwigFunction('asset', function ($path) use ($url) {
            return $url['url'] . $url['assets'] . "/$path";
        });
        $crsf = new TwigFunction("crsf", function () {
           $token = Token::getToken(url: $_SERVER['REQUEST_URI']);
           $_SESSION['_crsf'] = serialize($token);

           return $token->token;
        });

        $twig->addFunction($asset);
        $twig->addFunction($furl);
        $twig->addFunction($crsf);
        self::$twig = $twig;
        return $twig;
    }
}