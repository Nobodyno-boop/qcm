<?php

namespace Vroom\Utils;

/**
 *
 * @author Nobody
 *
 */
class ArrayUtils
{
    /**
     * @var array
     */
    private array $array;

    /**
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * We look if $path ins't empty or just contains a "."
     *
     * @param string $path
     * @return bool
     */
    private function isValidPath(string $path): bool
    {
        return !(empty($path) || $path === ".");
    }

    /**
     * Return search in the array with a path
     * Return null if the ins't exist
     * ```php
     * ArrayUtils::from([...])->get('user.id');
     * ```
     *
     * @param string $path
     * @return mixed return null if ins't exist
     */
    public function get(string $path) :mixed {
        if(!$this->isValidPath($path)){
            return null;
        }
        // if the does not contains "." we just put $path in a array.
        $keys = str_contains($path, ".") ? explode(".",$path) : [$path];

        $result = $this->array;
        foreach ($keys as $key){
            if(!isset($result[$key])){
                return null;
            }
            $result = $result[$key];
        }
        return $result;
    }

    /**
     * We use the get method and if its null we just return default value
     *
     *
     * @see ArrayUtils::get() for the base value
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function getOrDefault(string $path, mixed $default): mixed
    {
        $get = $this->get($path);

        return $get ? $get : $default;
    }

    /**
     * Return a new instance of ArrayUtils
     * @param array $array
     * @return ArrayUtils
     */
    public static function from(array $array): ArrayUtils
    {
        return new ArrayUtils($array);
    }
}