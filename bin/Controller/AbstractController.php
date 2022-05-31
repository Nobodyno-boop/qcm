<?php

namespace Vroom\Controller;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Vroom\Container\Container;
use Vroom\Orm\Model\Model;
use Vroom\Router\Request;
use Vroom\Router\Response;
use Vroom\Security\Token;
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
    protected function request(): Request
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
        if ($key) {
            if (!isset($_SESSION[$key])) {
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
            if (get_parent_class($value) === Model::class) {
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

    public function getRole()
    {
        if (!$this->isLogin()) {
            return "";
        }
        return $this->getSession("user")['role'] ?? "";
    }

    /**
     * Get a new instance of Response
     * @return Response
     * @see Response
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
        return $this->request()->getRoute()->getPath();
    }


    /**
     * Make a fresh CRSF Token and return it
     *
     * The token is putting in the session
     * @return string
     */
    public function getToken($url = ''): string
    {
        if (empty($url)) {
            $url = $this->url();
        }
        $token = Token::getToken(url: $url);

        $this->addSession("_crsf", $token);

        return $token->token;
    }

    /**
     * Check if the Token is good
     *
     * @param string $token
     * @return bool
     * @see Token
     */
    public function matchToken(string $token): bool
    {
        $sessionToken = unserialize($this->getSession("_crsf"));
        if (get_class($sessionToken) === Token::class) {
            $url = $this->url();
            if ($sessionToken->match($token, $url)) {
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
     */
    public function renderView(string $view, array $context = [])
    {
        try {
            if (!str_ends_with($view, ".twig")) {
                $view = $view . ".twig";
            }
            $template = $this->twig()->load($view);
            $appContext = new AppContext($_SESSION, true, [
                "class" => get_class($this)
            ]);
            $template->display(["app" => $appContext, ...$context]);
        } catch (\Exception $e) {
            $this->response()->redirect("404");
        }
    }


}