<?php

namespace Vroom\Controller;

use Twig\Environment;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Repository;
use Vroom\Router\Request;
use Vroom\Router\Response;
use Vroom\Security\Token;
use Vroom\Utils\Container;
use Vroom\View\AppContext;

class AbstractController
{
    /**
     * @var Request the current request
     */
    private Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the current request
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
        if($key){
            if(!isset($_SESSION[$key])){
                return null;
            }
            return $_SESSION[$key];
        }

        return $_SESSION;
    }

    /**
     * Store object in the session with key => value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function addSession(string $key, mixed $value)
    {
        if (is_object($value)) {
            if(get_parent_class($value) === Model::class){
                $value = $value->serialize();
            } else $value = serialize($value);

        }
        $_SESSION[$key] = $value;
    }

    /**
     * Check if the user is Login by checking the session
     * @return bool
     */
    protected function isLogin(): bool
    {
        return !empty($this->getSession("user"));
    }

    /**
     * Retrieve a new instance of Repository with Model instance
     * @param $class
     * @return Repository
     */
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

    /**
     * Get a new instance of Response
     * @see Response
     * @return Response
     */
    public function response(): Response
    {
        return new Response();
    }

    /**
     * Get the twig environment
     * @return Environment
     */
    protected function twig(): Environment
    {
        return Container::get("_twig");
    }

    /**
     * Get the url of the current request
     * @return string
     */
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

    /**
     * Check if the Token is good
     *
     * @see Token
     * @param string $token
     * @return bool
     */
    public function matchToken(string $token): bool
    {
        $sessionToken = unserialize($this->getSession("_crsf"));
        if (get_class($sessionToken) === Token::class) {
            $url = $this->url();
            if(!str_starts_with($url, "/")){
                $url = "/".$url;
            }
            if($sessionToken->match($token, $url)){
                $this->getToken();
                return true;
            }
            return false;
        }

        return false;
    }

    /**
     * Render the view of twig file
     *
     * @param string $view
     * @param array $context
     * @return void
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderView(string $view, array $context = [])
    {
        if(!str_ends_with($view, ".twig")){
            $view = $view.".twig";
        }
        $template = $this->twig()->load($view);
        $appContext = new AppContext($_SESSION, true, [
            "class" => get_class($this)
        ]);
        $template->display(["app" => $appContext, ...$context]);
    }


}