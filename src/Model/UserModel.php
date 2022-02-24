<?php

namespace App\Model;


use Vroom\Orm\decorator\Column;
use Vroom\Orm\decorator\Entity;
use Vroom\Orm\decorator\Type;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Types;

#[Entity('User')]
class UserModel extends Model
{

    #[
        Column('id', Types::int),
    ]
    private int $id;

    #[
        Column('name', Types::varchar),
    ]
    private string $name;

    public function __construct()
    {
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }




}