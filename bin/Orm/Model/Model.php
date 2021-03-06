<?php

namespace Vroom\Orm\Model;

use JetBrains\PhpStorm\Pure;
use PDO;
use ReflectionClass;
use Vroom\Container\Container;
use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Orm\Sql\Sql;

class Model
{
    protected bool $isSave = false;


    public function save()
    {

        $query = "";
        if ($this->isSave) {
            $rid = self::getModelId();
            if ($rid) {
                $id = $this->getVariable($rid);
                $query = (string)$this->query()->update($this)->where(['id' => $id]);
                self::getSQL()->query($query);
            }
        } else {
            $query = (string)$this->query()->insert($this);
            $pdo = self::getSQL()->query($query);
            if ($pdo) {
                $id = self::getSQL()->getCon()->lastInsertId();
                if ($id) {
                    $modelId = self::getModelId();
                    call_user_func([$this, 'set' . Model::varName($modelId->getName())], intval($id));
                }
            }
        }

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

    public static function custom(): QueryBuilder
    {
        return QueryBuilder::fromModel(static::class);
    }

    public static function runQuery(QueryBuilder $q): static|array|null
    {
        $stmt = self::getSQL()->query($q);
        if (!$stmt) {
            return null;
        }
        $var = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$var) {
            return null;
        }
        $count = $stmt->rowCount();
        if ($count === 1) {
            return self::toModel($var[0], static::class);
        } else if ($count > 1) {
            $objs = [];
            foreach ($var as $data) {
                $obj = self::toModel($data, static::class);
                if ($obj) {
                    $objs[] = $obj;
                }
            }
            return $objs;
        }
        return null;
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

    protected static function getModelId(): Column|null
    {
        $model = Models::get(static::class);
        if ($model) {
            $properties = array_values(array_filter($model['properties'], function ($el) {
                if ($el->getType() === Types::ID) {
                    return $el;
                }
            }));
            if (!empty($properties)) {
                return $properties[0];
            }
        }
        return null;
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
     * @param string|Column $row
     * @return mixed
     */
    public function getVariable(string|Column $row): mixed
    {
        try {
            if (!is_string($row)) {
                $row = $row->getName();
            }

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
     * Return can be null be carefully
     * @param mixed $value
     * @return static|null
     * @see Types
     */
    public static function find(mixed $value = null): static|null
    {
        return self::_find($value, static::class);
    }

    public static function findAll(mixed $value = null, $limit = 10, $offset = 0, $order = "ASC"): array|null
    {
        return self::_findAll($value, static::class, $limit, $offset, $order);
    }

    public static function count(array $where = []): int
    {
        $q = QueryBuilder::fromModel(static::class)->select("COUNT(*)");
        if (!empty($where)) {
            $q->where($where);
        }
        $query = self::getSQL()->query($q)->fetch(PDO::FETCH_COLUMN);
        if (is_int($query)) {
            return $query;
        } else {
            return -1;
        }
    }

    public function delete(): bool|\PDOStatement
    {
        $data = [self::getModelId()->getName() => $this->getVariable(self::getModelId())];
        $q = $this->query()->delete()->where($data);
        return self::getSQL()->query($q)->fetch(PDO::FETCH_COLUMN);
    }


    private static function _findAll(mixed $value, $class, $limit = 10, $offset = 0, $order = "ASC")
    {
        if (is_array($value)) {
            $q = QueryBuilder::fromModel($class)->where($value)->limit($limit)->offset($offset)->order(self::getModelId()->getName(), $order);
            $stmt = self::getSQL()->query($q);
            if (!$stmt) {
                return null;
            }

            $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$datas) {
                return null;
            }
            $objs = [];
            foreach ($datas as $data) {
                $obj = self::toModel($data, static::class);
                if ($obj) {
                    $objs[] = $obj;
                }
            }
            return $objs;
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
                $q = QueryBuilder::fromModel($class)->where($value)->limit($limit)->offset($offset)->order($key, $order);
                $stmt = static::getSQL()->query($q);
                $var = $stmt->fetch(PDO::FETCH_ASSOC);
                return self::toModel($var, $class);
            } else {
                $q = QueryBuilder::fromModel($class)->limit($limit)->offset($offset)->order(self::getModelId()->getName(), $order);
                $stmt = static::getSQL()->query($q);
                $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $objs = [];

                foreach ($datas as $data) {
                    $obj = self::toModel($data, static::class);
                    if ($obj) {
                        $objs[] = $obj;
                    }
                }
                return $objs;
            }
        }
        return null;
    }


    private static function _find(mixed $value, $class, $limit = 10, $offset = 0): object|null
    {
        if (is_array($value)) {
            $q = QueryBuilder::fromModel($class)->where($value)->limit($limit)->offset($offset);;
            $stmt = static::getSQL()->query($q);
            if (!$stmt) {
                return null;
            }
            $var = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$var) {
                return null;
            }
            return self::toModel($var, $class);
        } else {
            if ($value !== null) {
                $key = "";
                $model = Models::get($class);

                if (!$model) {
                    return null;
                }

                $keys = array_filter($model['properties'], function ($e) {
                    if ($e->getType() == Types::ID) {
                        return $e;
                    }
                });
                if (!$keys) {
                    return null;
                }

                $key = $keys[0]->getName() ?? "";
                if (!$key) {
                    return null;
                }

                $q = QueryBuilder::fromModel($class)->where([$key => $value])->limit($limit)->offset($offset);;
                $stmt = static::getSQL()->query($q);
                if (!$stmt) {
                    return null;
                }
                $var = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$var) {
                    return null;
                }
                return self::toModel($var, $class);
            }
        }
        return null;
    }

}