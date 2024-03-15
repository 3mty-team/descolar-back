<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Data\Repository\User\DeactivationUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeactivationUserRepository::class)]
#[ORM\Table(name: "user_deactivation")]
class DeactivationUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "userdeactivation_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "userdeactivation_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "userdeactivation_isfinal", type: "boolean", options: ["default" => 0])]
    private bool $isFinal;

    #[ORM\Column(name: "userdeactivation_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function isFinal(): bool
    {
        return $this->isFinal;
    }

    public function setIsFinal(bool $isFinal): void
    {
        $this->isFinal = $isFinal;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}