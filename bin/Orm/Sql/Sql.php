<?php

namespace Vroom\Orm\Sql;

use PDO;

class Sql
{
    private PDO $con;


    public function connect()
    {
        try{
            $this->con = new PDO("mysql:dbname=qcm;host=localhost", "root", "");
        }catch (\PDOException $e){
            die($e->getMessage());
        }
    }

    /**
     * @return PDO
     */
    public function getCon(): PDO
    {
        return $this->con;
    }

}