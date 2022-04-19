<?php

namespace App\Model;

use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Decorator\Entity;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Types;

#[Entity("QcmStats")]
class QcmStats extends Model
{
    #[Column("id", Types::ID)]
    private int $id;
    #[Column("qcm", Types::ONE_TO_ONE, join: "Qcm")]
    private Qcm $qcm;
    #[Column("user", Types::ONE_TO_ONE, join: "User")]
    private User $user;
    #[Column("data", Types::JSON)]
    private array $data;
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
     * @return Qcm
     */
    public function getQcm(): Qcm
    {
        return $this->qcm;
    }

    /**
     * @param Qcm $qcm
     */
    public function setQcm(Qcm $qcm): void
    {
        $this->qcm = $qcm;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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