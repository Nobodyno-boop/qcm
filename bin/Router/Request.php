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

    public function query(string $key = ""): mixed
    {
        $parse = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        $obj = [];
        if($parse != null){
            if(str_contains($parse, "&")){
                $objets = explode("&", $parse);
                $objets = array_map(function ($e){
                    $cut = explode("=", $e);
                    return [$cut[0] => $cut[1]];
                }, $objets);
                $obj = array_merge(...$objets);
            } else {
                $objets = explode("=", $parse);
                $obj = [$objets[0] => $objets[1]];
            }
        }

        return $obj[$key] ?? $obj;
    }

    public function redirect($url)
    {

    }
}