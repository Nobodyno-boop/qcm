<?php

namespace App\Model;

use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Model\Types;

class Qcm
{
//    #[Column("id", Types::)]
    private int $id;
    private string $title;
    private array $data;
    private User $author;
    private string $updatedAt;
    private string $createdAt;
}