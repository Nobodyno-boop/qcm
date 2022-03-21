<?php

namespace Vroom\Controller;

use Twig\Environment;
use Vroom\Orm\Repository;
use Vroom\Router\Request;
use Vroom\Router\Response;
use Vroom\Security\Token;
use Vroom\Utils\Container;

class AbstractController
{
    private Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get key session or full array if key is null
     * @param string|null $key
     * @return array|mixed
     */
    protected function getSession(string $key = null): mixed
    {
        return $key == null ? $_SESSION : $_SESSION[$key];
    }

    protected function addSession(string $key, mixed $value)
    {
        if (is_object($value)) {
            $value = serialize($value);
        }
        $_SESSION[$key] = $value;
    }

    protected function isLogin(): bool
    {
        return !empty($this->getSession("user"));
    }

    protected function repository($class): Repository
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        if (is_string($class)) {
            return new Repository($class);
        }
        throw new \Error("Could not get model class");
    }

    public function response(): Response
    {
        return new Response();
    }

    public function twig(): Environment
    {
        return Container::get("_twig");
    }

    public function url(): string
    {
        return $this->getRequest()->getRoute()->getPath();
    }


    /**
     * Make a fresh CRSF Token and return it
     *
     * The token is putting in the session
     * @return string
     */
    public function getToken(): string
    {
        $token = Token::getToken(url: $this->url());

        $this->addSession("_crsf", $token);

        return $token->token;
    }


    public function matchToken(string $token)
    {
        $sessionToken = unserialize($this->getSession("_crsf"));
        if (get_class($sessionToken) === Token::class) {
            return $sessionToken->match($token, $this->url());
        }

        return false;
    }

    public function renderView(string $view, array $context = [])
    {
        $template = $this->twig()->load($view);

        $template->display(["app" => ["id" => 1], ...$context]);
    }


}