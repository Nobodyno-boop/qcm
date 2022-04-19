<?php

namespace Vroom\Orm\Model;

use App\Model\User;
use JetBrains\PhpStorm\Pure;
use PDO;
use ReflectionClass;
use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Orm\Sql\Sql;
use Vroom\Utils\Container;

class Model
{
    protected bool $isSave = false;

    public function __construct()
    {
    }

    public function save()
    {

        $query = "";
        if ($this->isSave) {
            $query = (string)$this->query()->update($this);
        } else {
            $query = (string)$this->query()->insert($this);
        }
//        dd($query);
        self::getSQL()->query($query);
    }

    #[Pure]
    public function newInstance()
    {
        return new $this;
    }

    /**
     * @return bool
     */
    public function isSave(): bool
    {
        return $this->isSave;
    }

    /**
     * @param bool $isSave
     */
    public function setIsSave(bool $isSave): void
    {
        $this->isSave = $isSave;
    }

    public function query(): QueryBuilder
    {
        return QueryBuilder::fromModel($this);
    }


    private static function getSQL(): Sql
    {
        return Container::get("_db");
    }


    public function serialize(): array
    {
        $model = Models::get($this);
        $json = [];
        $vars = $this->_getvars();
        foreach ($model['properties'] as $property) {
            $value = null;
            if ($property->isNullable()) {
                if (isset($vars[$property->getName()])) {
                    $value = call_user_func([$this, 'get' . Model::varName($property->getName())]) ?? null;
                }
            } else {
                $value = call_user_func([$this, 'get' . Model::varName($property->getName())]) ?? null;
            }
            if ($value) {
                $json[$property->getName()] = $value;
            }
        }
        return $json;
    }


    /**
     * @param string $row
     * @return string
     */
    public static function varName(string $row): string
    {
        if (str_contains($row, "_")) {
            $split = explode("_", $row);
            $word = array_shift($split);
            foreach ($split as $key) {
                $word .= ucfirst($key);
            }
            return $word;
        }
        return ucfirst($row);
    }


    /**
     * Return a variable based on a instance of Model
     *
     * @param string $row
     * @return mixed
     */
    public function getVariable(string $row): mixed
    {
        try {
            $name = "get" . self::varName($row);
            $result = static::$name();
            if ($result) {
                return $result;
            }
        } catch (\Error $e) { // avoid didn't get the method
            return null;
        }

        return null;
    }

    public function _getvars(): array
    {
        return get_object_vars($this);
    }


    private static function toModel($var, $clasz = ""): ?object
    {
        if (is_array($var)) {
            try {
                if (empty($clasz)) {
                    $clasz = static::class;
                }
                $class = new ReflectionClass($clasz);
                $model = Models::get($clasz);
                $m = $class->newInstance();
                foreach ($model['properties'] as $k) {
                    $name = $k->getName();
                    if (isset($var[$name])) {
                        $value = match ($k->getType()) {
                            Types::JSON => json_decode($var[$name], true),
                            Types::MANY_TO_ONE => self::manyToOne($var[$name], $k->getJoin()),
                            default => $var[$name]
                        };
                        call_user_func_array([$m, 'set' . Model::varName($name)], [$value]);
                    }
                }
                call_user_func_array([$m, 'setIsSave'], [true]);
                return $m;
            } catch (\ReflectionException $e) {
            }
        }
        return null;
    }

    private static function manyToOne($id, $name)
    {
        $class = Models::get($name);
        if (!empty($class)) {
            return self::_find($id, $class['class']);
        }
        return null;
    }


    /**
     * Find object in database and turn into her model
     * By default we search the Type "ID" so if you don't have the type id please a key value array
     *
     * Sample with a User model with a Types::ID
     * ```php
     * User::find(1)
     * ```
     * Sample with key value
     * ```php
     * User::find(['email' => "sample@test.com"])
     * ```
     * Return can be null be carefull
     * @param mixed $value
     * @return static|null
     * @see Types
     */
    public static function find(mixed $value): static|null
    {
        return self::_find($value, static::class);
    }


    private static function _find(mixed $value, $class): object|null
    {
        if (is_array($value)) {
            $q = QueryBuilder::fromModel($class)->where($value);
            $stmt = static::getSQL()->query($q);
            $var = $stmt->fetch(PDO::FETCH_ASSOC);
            return self::toModel($var, $class);
        } else {
            if ($value !== null) {
                $key = "";
                $model = Models::get($class);
                $keys = array_filter($model['properties'], function ($e) {
                    if ($e->getType() == Types::ID) {
                        return $e;
                    }
                });
                if (count($keys) == 1) {
                    $key = $keys[0]->getName();
                }
                $q = QueryBuilder::fromModel($class)->where([$key => $value]);
                $stmt = static::getSQL()->query($q);
                $var = $stmt->fetch(PDO::FETCH_ASSOC);
                return self::toModel($var, $class);
            }
        }
        return null;
    }

}