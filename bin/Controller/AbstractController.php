<?php

namespace Vroom\Controller;

use Vroom\Orm\Repository;
use Vroom\Orm\Sql\Sql;
use Vroom\Router\Request;
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
        return $key == null ? $_SESSION : $_SESSION[$key] ?? [];
    }

    protected function addSession(string $key, mixed $value)
    {
        $_SESSION[$key] = $value;
    }

    protected function isLogin(): bool
    {
        return !empty($this->getSession("user"));
    }

    protected function repository($class): Repository
    {
        if(is_object($class)){
            $class = get_class($class);
        }
        if(is_string($class)){
            return new Repository($class);
        }
        throw new \Error("Could not get model class");
    }


}