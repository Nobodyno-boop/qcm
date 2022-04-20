<?php

namespace Vroom\Orm\Sql;

use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Models;
use Vroom\Orm\Model\Types;

class QueryBuilder
{
    private $model;
    private $fields = [];
    private $cond = [];
    private $from = [];
    private $word = "";
    private $values = [];
    private $limit = 0;
    private $offset = 0;

    /**
     * @param string|null $model
     */
    public function __construct(string $model = null)
    {
        if ($model != null) {
            $models = Models::readModel($model);
            if (!empty($models)) {
                $this->model = $models;
            }
        }
    }

    public static function newInstance(string $model = null): QueryBuilder
    {
        return new QueryBuilder($model);
    }

    public static function fromModel(Model|string $model): QueryBuilder
    {
        if (is_object($model)) {
            return new QueryBuilder(get_class($model));
        } else return new QueryBuilder($model);
    }

    public function select(string ...$select): QueryBuilder
    {
        if (empty($select)) {
            foreach ($this->model['properties'] as $k) {
                $this->fields[] = $k->getName();
            }
        } else {
            $this->fields = $select;
        }
        $this->word = "SELECT";
        return $this;
    }

    public function where(array $where): QueryBuilder
    {
        foreach ($where as $k => $v) {
            $this->cond[] = $k . " = '" . $v . "'";
        }
        return $this;
    }

    public function from(?string $table = "", ?string $alias = null): QueryBuilder
    {
        $name = $table == "" ? strtolower($this->model['entity']->getName()) : "";

        if ($alias === null) {
            $this->from[] = $name;
        } else {
            $this->from[] = "${$name} AS ${alias}";
        }

        return $this;
    }


    public function delete(): QueryBuilder
    {
        $this->word = "DELETE";
        return $this;
    }

    public function offset($offset): QueryBuilder
    {
        $this->offset = $offset;
        return $this;
    }


    public function update(array|Model $update): QueryBuilder
    {
        $this->word = "UPDATE";

        if (is_array($update)) {
            foreach ($update as $k => $v) {
                $this->fields[] = $k . " = '" . $v . "'";
            }
        } else { // when is the object
            $vars = $update->_getvars();
            foreach ($this->model['properties'] as $item) {
                if (!($item->getType() == Types::ID)) {
                    $value = null;
                    if ($item->isNullable()) {
                        if (isset($vars[Model::varName($item->getName())])) {
                            $value = call_user_func(array($update, 'get' . Model::varName($item->getName())));
                        }
                    } else {
                        $value = call_user_func(array($update, 'get' . Model::varName($item->getName())));
                    }

                    if ($value) {
                        $this->fields[] = $item->getName() . " = '" . $value . "'";
                    }
                }
            }
        }

        return $this;
    }

    public function limit(int $limit = 1): QueryBuilder
    {
        $this->limit = $limit;
        return $this;
    }

    public function insert(array|Model $insert): QueryBuilder
    {
        $this->word = "INSERT";
        if (is_array($insert)) {
            foreach ($insert as $k => $v) {
                $this->values['keys'][] = $k;
                $this->values['values'][] = "'" . $v . "'";
            }
        } else { //model
            $vars = $insert->_getvars();
            foreach ($this->model['properties'] as $item) {
                if (!($item->getType() === Types::ID)) {
                    $value = null;
                    if ($item->isNullable()) {
                        if (isset($vars[Model::varName($item->getName())])) {
                            $value = call_user_func(array($insert, 'get' . Model::varName($item->getName())));
                        }
                    } else {
                        $value = call_user_func(array($insert, 'get' . Model::varName($item->getName())));
                    }
                    if ($value) {
                        $value = match ($item->getType()) {
                            Types::JSON => json_encode($value),
                            default => $value
                        };

                        if ($item->getType() === Types::ONE_TO_ONE || $item->getType() === Types::ONE_TO_MANY || $item->getType() === Types::MANY_TO_ONE || $item->getType() === Types::MANY_TO_MANY) {
                            if (is_object($value)) {
                                $type = $this->getModelId($value);
                                if ($type) {
                                    $value = call_user_func([$value, 'get' . Model::varName($type->getName())]);

                                    $this->values['keys'][] = $item->getName();
                                    $this->values['values'][] = "'" . $value . "'";
                                } // error you must specify a type ID
                            }
                        } else {
                            $this->values['keys'][] = $item->getName();
                            $this->values['values'][] = "'" . $value . "'";
                        }

                    }
                }
            }
        }
        return $this;
    }


    private function getModelId($model)
    {
        $model = Models::get(get_class($model));

        $find = array_values(array_filter($model['properties'], function ($el) {
            if ($el->getType() === Types::ID) {
                return $el;
            }
        }));

        return (empty($find) === true) ? null : $find[0];
    }

    public function __toString(): string
    {
        $query = "";
        $table = empty($this->from) ? strtolower($this->model['entity']->getName()) : implode(", ", $this->from);
        if (!empty($table)) {
            $w = empty($this->cond) ? '' : ' WHERE ' . implode(" AND ", $this->cond);
            $selector = empty($this->fields) ? " *" : implode(", ", $this->fields);

            switch (strtolower($this->word)) {
                case "insert":
                    $keys = "(" . implode(", ", $this->values['keys']) . ")";
                    $values = "(" . implode(", ", $this->values['values']) . ")";

                    $query = "INSERT INTO " . $table . " "
                        . $keys . " VALUES" .
                        $values;

                    break;
                case "update":
                    $query = $this->word . " " . $table .
                        " SET " . implode(", ", $this->fields)
                        . $w;
                    break;
                case "delete":
                    $query = $this->word . " "
                        . $table . " "
                        . $w;
                    break;
                default:
                case "select":
                $limit = $this->limit != 0 ? " LIMIT " . $this->limit : "";
                $offset = $this->offset != 0 ? " OFFSET " . $this->offset : "";
                $query = "SELECT " . $selector . " FROM "
                    . $table
                    . $w . $limit . $offset;

                break;
            }
        }

        return $query;
    }
}