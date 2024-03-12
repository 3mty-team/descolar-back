<?php

namespace Descolar\Data\Entities\Configuration;

use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Configuration\LoginRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LoginRepository::class)]
#[ORM\Table(name: "login")]
class Login
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "login_password", type: "string", length: 255)]
    private string $password;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}