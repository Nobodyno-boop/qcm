<?php

namespace App\Model;


use Vroom\Orm\Decorator\Column;
use Vroom\Orm\Decorator\Entity;
use Vroom\Orm\Model\Model;
use Vroom\Orm\Model\Types;
use Vroom\Utils\Form;

#[Entity('User')]
class User extends Model
{

    #[
        Column('id', Types::ID),
    ]
    private int $id;

    #[
        Column('username', Types::VARCHAR),
    ]
    private string $username;

    #[
        Column('role', Types::VARCHAR, nullable: true),
    ]
    private string $role;

    #[Column("email", Types::VARCHAR, formType: Form::TYPE_EMAIL)]
    private string $email;

    #[Column("password", Types::VARCHAR, formType: Form::TYPE_PASSWORD)]
    private string $password;

    #[Column("updated_at", Types::DATETIME, nullable: true)]
    private string $updated_at;

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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     */
    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
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