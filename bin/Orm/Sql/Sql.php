<?php

namespace Vroom\Orm\Sql;

use PDO;
use Vroom\Utils\Container;

class Sql
{
    private PDO $con;

    public function __construct()
    {
        $this->connect();
    }


    public function connect()
    {
        try{
            /**
             * @var array $db
             */
            $db = Container::get("_config")->getConfig()['db'];
            $this->con = new PDO("mysql:dbname=".$db["database"].";host=".$db['host'], $db['user'], $db['password']);
        }catch (\PDOException $e){
            die($e);
        }
    }

    /**
     * @return PDO
     */
    public function getCon(): PDO
    {
        return $this->con;
    }

    public function query(string $query): bool|\PDOStatement
    {
        return $this->getCon()->query($query);
    }
}