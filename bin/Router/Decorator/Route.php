<?php

namespace Vroom\Router\Decorator;

use Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Route
{
    private string $url;
    private string $name;
    private array $methods;

    /**
     * @param string $url
     * @param string|null $name
     * @param array $methods
     */
    public function __construct(string $url, ?string $name = "", array $methods = ["GET"])
    {
        $this->url = $url;
        $this->name = $name;
        $this->methods = $methods;
    }


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }


}