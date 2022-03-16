<?php

namespace Vroom\Router;

class Request
{
    private Route $route;
    private mixed $_body;
    private array|false $header;
    private string $method;
    /**
     * @param Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
        $this->header = getallheaders();
        $this->_body = $this->getBodyByHeader();
    }


    private function getBodyByHeader(){
        $input = file_get_contents("php://input");
        return match ($this->header['Content-Type'] ?? "") {
            "application/json" => json_decode($input),
            default => $input,
        };
    }


    /**
     * @return mixed
     */
    public function getBody(): mixed
    {
        return $this->_body;
    }


    public function redirect($url)
    {

    }
}