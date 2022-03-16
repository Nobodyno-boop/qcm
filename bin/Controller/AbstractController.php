<?php

namespace Vroom\Controller;

use Vroom\Router\Request;

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

}