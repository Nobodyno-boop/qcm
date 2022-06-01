<?php

namespace Vroom\View;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Markup;
use Twig\TwigFunction;
use Vroom\Config\Config;
use Vroom\Container\Container;
use Vroom\Router\Router;
use Vroom\Security\Token;
use Vroom\Utils\Form;

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
        $config = Config::container();
        $loader = new FilesystemLoader($config->get('template.dir'));
        $twig = new Environment($loader, ['debug' => true]);
        $twig->getExtension(\Twig\Extension\CoreExtension::class)->setTimezone('Europe/Paris');
        $twig->addExtension(new \Twig\Extension\DebugExtension());
        $url = $config->get("site");

        // function url(path or url)
        $furl = new TwigFunction('url', function ($path) use ($url) {
            $path = Router::getFromPrefix($path) ?? $path;
            if (is_object($path)) {
                $path = $path->getPath();
            }
            if (!str_starts_with($path, "/")) {
                $path = "/" . $path;
            }
            return $url['url'] . $path;
        });
        // function asset(url)
        $asset = new TwigFunction('asset', function ($path) use ($url) {
            return $url['url'] . $url['assets'] . "/$path";
        });
        // crsf function maybe can be remove?
        $crsf = new TwigFunction("crsf", function () {
            $token = Token::getToken(url: $_SERVER['REQUEST_URI']);
            $_SESSION['_crsf'] = serialize($token);

            return $token->token;
        });

        // form(form)
        $form = new TwigFunction("form", function($form, $url = ""){
            if(get_class($form) === Form::class){
                return new Markup($form->toView($url), "UTF-8");
            }
            return "";
        });

        $twig->addFunction($form);
        $twig->addFunction($asset);
        $twig->addFunction($furl);
        $twig->addFunction($crsf);
        self::$twig = $twig;
        return $twig;
    }
}