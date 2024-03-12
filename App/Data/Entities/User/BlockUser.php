<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Data\Repository\User\BlockUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlockUserRepository::class)]
#[ORM\Table(name: "user_block")]
class BlockUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_blocking_id", referencedColumnName: "user_id")]
    private User $blocking; # A, the person blocking B

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_blocked_id", referencedColumnName: "user_id")]
    private User $blocked; # B, the person being blocked by A

    #[ORM\Column(name: "userblock_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "userblock_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getBlocking(): User
    {
        return $this->blocking;
    }

    public function setBlocking(User $blocking): void
    {
        $this->blocking = $blocking;
    }

    public function getBlocked(): User
    {
        return $this->blocked;
    }

    public function setBlocked(User $blocked): void
    {
        $this->blocked = $blocked;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
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