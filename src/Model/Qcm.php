<?php

namespace App\Model;

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