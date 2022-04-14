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
    public function get(string $path): mixed
    {
        if (!$this->isValidPath($path)) {
            return null;
        }

        if (count($this->array) >= 1) {
            // if the does not contains "." we just put $path in a array.
            $keys = str_contains($path, ".") ? explode(".", $path) : [$path];

            $result = $this->array;
            foreach ($keys as $key) {
                if (!isset($result[$key])) {
                    return null;
                }
                $result = $result[$key];
            }
            return $result;
        }
        return null;
    }

    /**
     * We use the get method and if its null we just return default value
     *
     *
     * @param string $path
     * @param mixed $default
     * @return mixed
     * @see ArrayUtils::get() for the base value
     */
    public function getOrDefault(string $path, mixed $default): mixed
    {
        $get = $this->get($path);

        return $get !== null ? $get : $default;
    }

    /**
     * Return if the array is empty or not
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !(count($this->array) >= 1);
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

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->array;
    }
}