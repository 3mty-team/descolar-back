<?php

namespace Descolar\Data\Entities\Group;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Group\GroupMessageLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupMessageLikeRepository::class)]
#[ORM\Table(name: "group_message_like")]
class GroupMessageLike
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: GroupMessage::class)]
    #[ORM\JoinColumn(name: "groupmessage_id", referencedColumnName: "groupmessage_id")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "groupmessagelike_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $likeDate;

    #[ORM\Column(name: "groupmessagelike_isactive", type: "boolean", options: ["default" => 1])]
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

    public function getLikeDate(): ?DateTimeInterface
    {
        return $this->likeDate;
    }

    public function setLikeDate(?DateTimeInterface $likeDate): void
    {
        $this->likeDate = $likeDate;
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