<?php

namespace Vroom\Orm\Sql;

use PDO;
use Vroom\Config\Config;
use Vroom\Container\Container;
use Vroom\Container\IContainer;

class Sql implements IContainer
{
    private PDO $con;

    public function __construct()
    {
        $this->connect();
    }


    public function connect()
    {
        try {
            /**
             * @var array $db
             */
            $db = Config::container()->get("db");
            $this->con = new PDO("mysql:dbname=" . $db["database"] . ";host=" . $db['host'], $db['user'], $db['password']);
        } catch (\PDOException $e) {
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

    public static function getContainerNamespace(): string
    {
        return "_db";
    }

    public static function container(): static
    {
        return Container::get(self::getContainerNamespace());
    }
}