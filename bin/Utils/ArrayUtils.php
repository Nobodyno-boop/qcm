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
     *
     * ```php
     * ArrayUtils::from([...])->get('user.id');
     * ```
     *
     * @param string $path
     * @return mixed
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
     * Return a new instance of ArrayUtils
     * @param array $array
     * @return ArrayUtils
     */
    public static function from(array $array): ArrayUtils
    {
        return new ArrayUtils($array);
    }
}