<?php

namespace App\Model;

use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Decorator\Entity;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Types;

#[Entity('Qcm')]
class Qcm extends Model
{
    #[Column("id", Types::ID)]
    private int $id;

    #[Column("title", Types::VARCHAR)]
    private string $title;

    #[Column("data", Types::JSON)]
    private array $data;

    #[Column("author", Types::MANY_TO_ONE, join: "User")]
    private User $author;

    #[Column("updated_at", Types::DATETIME, nullable: true)]
    private string $updatedAt;

    #[Column("created_at", Types::DATETIME, nullable: true)]
    private string $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     */
    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


}