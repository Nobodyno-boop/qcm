<?php

namespace Vroom\Router;


class Route
{
    private string $url;
    private string $controller;
    private string $controllerMethod;
    private string $method;
    private $matches = [];
    private array $params = [];
    private string $prefix;
    private array $currentMatchs;

    /**
     * @param array $data
     */
    public function __construct(array $data, string $controller, string $method)
    {
        $this->url = trim($data['url'], "/");
        $this->prefix = $data['prefix'];
        $this->controllerMethod = $data['name'];
        $this->controller = $controller;
        $this->method = $method;
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
        $this->currentMatchs = [];
        $url = trim($url, '/');
        $path = preg_replace_callback('/:([\w]+)/', [$this, 'paramMatch'], $this->url);
        $regex = "#^$path#i";
        if (!preg_match($regex, $url, $matches)) {
            return false;
        }
        array_shift($matches);
        $this->matches = $matches;
        if(!empty($this->currentMatchs)){
            if(count($matches) == count($this->currentMatchs)){ // when correct url
                for($i =0; $i < count($matches); $i++){
                    $this->params[$this->currentMatchs[$i]] = $matches[$i];
                }
            }
        }
        return true;
    }

    private function paramMatch($match): string
    {
        $this->currentMatchs[] = $match[1];
        if (isset($this->params[$match[1]])) {
            return '(' . $this->params[$match[1]] . ')';
        }
        return '([^/]+)';
    }

    public function getUrlWith($params)
    {
        $url = $this->url;
        foreach ($params as $k => $v) {
            $url = str_replace(":$k", $v, $url);
        }
        return $url;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    public function with($param, $regex)
    {
        $this->params[$param] = str_replace('(', '(?:', $regex);
        return $this; // On retourne tjrs l'objet pour enchainer les arguments
    }

    public function call()
    {
        dump($this);
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return mixed|string
     */
    public function getPrefix(): mixed
    {
        return $this->prefix;
    }

    /**
     * @return mixed|string
     */
    public function getMethod(): mixed
    {
        return $this->method;
    }

    /**
     * @return mixed|string
     */
    public function getControllerMethod(): mixed
    {
        return $this->controllerMethod;
    }


}
