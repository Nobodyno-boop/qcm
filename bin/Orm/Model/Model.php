<?php

namespace Vroom\Orm\Model;

use JetBrains\PhpStorm\Pure;
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

        $this->getSQL()->query($query);
    }

    #[Pure] public function newInstance()
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


    public function query(): QueryBuilder
    {
        return QueryBuilder::fromModel($this);
    }


    private function getSQL(): Sql
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
}