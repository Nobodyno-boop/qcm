<?php

namespace Vroom\Orm;

use PDO;
use ReflectionClass;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Models;
use Vroom\Orm\Model\Types;
use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Orm\Sql\Sql;
use Vroom\Utils\Container;

class Repository
{
    private string $model;

    /**
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }


    private function newQuery(): QueryBuilder
    {
        return QueryBuilder::newInstance($this->model);
    }

    public function get(mixed $value, $key = null)
    {

        if ($key === null) {
            $keys = array_filter($this->getModel()['properties'], function ($e) {
                if ($e->getType() == Types::ID) {
                    return $e;
                }
            });
            if (count($keys) == 1) {
                $key = $keys[0]->getName();
            }
        }

        $q = $this->newQuery()->where([$key => $value]);
        $stmt = $this->getSQL()->query($q);
        $var = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->toModel($var);
    }

    public function getAll(int $limit = 10): ?object
    {
        $q = $this->newQuery();
        $q->select()->limit($limit);
//        dump((string)$q);
        $stmt = $this->getSQL()->query($q);

        $var = $stmt->fetch(PDO::FETCH_ASSOC);
        return $this->toModel($var);
    }

    protected function getSQL(): Sql
    {
        return Container::get("_db");
    }

    private function getModel()
    {
        return Models::get($this->model);
    }

    public function toModel($var): ?object
    {
        if (is_array($var)) {
            try {
                $class = new ReflectionClass($this->model);
                $m = $class->newInstance();
                foreach ($this->getModel()['properties'] as $k) {
                    $name = $k->getName();
                    if (isset($var[$name])) {
                        call_user_func_array([$m, 'set' . Model::varName($name)], [$var[$name]]);
                    }
                }
                return $m;
            } catch (\ReflectionException $e) {
            }
        }
        return null;
    }

    public function findBy(string $key, mixed $value)
    {
        return $this->get($value, $key);
    }
}