<?php

namespace Vroom\Router;


class Route
{
    private string $path;
    private string $controller;
    private string $controllerMethod;
    private string $method;
    private array $var = [];
    private array $params = [];
    private string $name;


    public function __construct(array $data, string $controller, string $method)
    {
        $this->path = trim($data['url'], "/");
        $this->name = $data['prefix'];
        $this->controllerMethod = $data['name'] ?? "";
        $this->controller = $controller;
        $this->method = $method;
    }

    public function match(string $path, string $method): bool
    {
        $regex = $this->getPath();
        foreach ($this->getVarsNames() as $variable) {
            $varName = trim($variable, '{\}');
            $regex = str_replace($variable, '(?P<' . $varName . '>[^/]++)', $regex);
        }


        if ($method === $this->method && preg_match('#^' . $regex . '$#sD', self::trimPath($path), $matches)) {
            $values = array_filter($matches, static function ($key) {
                return is_string($key);
            }, ARRAY_FILTER_USE_KEY);
            foreach ($values as $key => $value) {
                $this->var[$key] = $value;
            }
            return true;
        }
        return false;
    }

    public function getName(): string
    {
        return $this->name;
    }


    public function getPath(): string
    {
        return $this->path;
    }

    public function getParameters(): array
    {
        return $this->params;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getVarsNames(): array
    {
        preg_match_all('/{[^}]*}/', $this->path, $matches);
        return reset($matches) ?? [];
    }

    public function hasVars(): bool
    {
        return $this->getVarsNames() !== [];
    }

    public function getVars(): array
    {
        return $this->var;
    }

    public static function trimPath(string $path): string
    {
        return rtrim(ltrim(trim($path), '/'), '/');
    }


    /**
     * @return mixed|string
     */
    public function getControllerMethod(): mixed
    {
        return $this->controllerMethod;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

}
