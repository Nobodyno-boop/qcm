<?php

namespace Vroom\Orm\Model;

use JetBrains\PhpStorm\Pure;
use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Orm\Sql\Sql;
use Vroom\Utils\Container;

class Model
{
    protected bool $isSave = false;

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
        foreach ($model['properties'] as $property) {
            $value = call_user_func([$this, 'get' . ucfirst($property->getName())]) ?? null;
            $json[$property->getName()] = $value;
        }
//        dump($json);
        return $json;
    }

}