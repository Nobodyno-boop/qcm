<?php

namespace Vroom\Utils;

class Container
{
    private static array $containers = [];


    public static function set(string $name, mixed $value) : void
    {
        self::$containers[$name] = $value;
    }

    public static function get(string $name)
    {
        return self::$containers[$name] ?? [];
    }

    public static function isEmpty(string $name): bool
    {
        return empty(self::get($name));
    }
}