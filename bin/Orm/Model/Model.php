<?php

namespace Vroom\Orm\Model;

use Vroom\Orm\Sql\QueryBuilder;
use Vroom\Orm\Sql\Sql;
use Vroom\Utils\Container;

class Model
{
    protected bool $isSave = false;

    public function save()
    {

        $query = "";
        if($this->isSave){
            $query = (string) $this->query()->update($this);
        } else {
            $query = (string) $this->query()->insert($this);
        }

        $this->getSQL()->query($query);
    }



    public function query(): QueryBuilder
    {
        return QueryBuilder::fromModel($this);
    }


    private function getSQL() : Sql
    {
        return Container::get("_db");
    }

}