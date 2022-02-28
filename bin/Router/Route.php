<?php

namespace Vroom\Router;

class Route
{
    private string $url;
    private string $controller;
    private $matches = [];
    private array $params = [];
    /**
     * @param string $url
     * @param string $controller
     */
    public function __construct(string $url, string $controller)
    {
        $this->url = trim($url, "/");
        $this->controller = $controller;
    }


    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }


    public function match($url): bool
    {
        $url = trim($url, '/');
        $path = preg_replace_callback('/:([\w]+)/', [$this, 'paramMatch'], $this->url);
        $regex = "#^$path#i";

        if(!preg_match($regex, $url, $matches)){
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        return true;
    }

    private function paramMatch($match): string
    {
        if(isset($this->params[$match[1]])){
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    public function getUrl($params){
        $url = $this->url;
        foreach($params as $k => $v){
            $url = str_replace(":$k", $v, $url);
        }
        return $url;
    }

    public function with($param, $regex){
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this; // On retourne tjrs l'objet pour enchainer les arguments
    }

    public function call()
    {
        dump($this);
    }

}