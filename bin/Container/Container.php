<?php

namespace Vroom\Container;

class Container
{
    private static array $containers = [];


    public static function setObject($obj)
    {
        if (is_object($obj)) {
            $interfaces = class_implements($obj);
            if ($interfaces) {
                if (in_array(IContainer::class, $interfaces)) {
                    self::set(call_user_func_array([$obj, 'getContainerNamespace'], []), $obj);
                }
            }
        }
    }

    public static function set(string $name, mixed $value): void
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