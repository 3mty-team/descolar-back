<?php

namespace Descolar\Data\Entities\Group;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Group\GroupMessageLikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: GroupMessageLikeRepository::class)]
#[ORM\Table(name: "group_message_like")]
#[Validate\Validate]
class GroupMessageLike
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: GroupMessage::class)]
    #[ORM\JoinColumn(name: "groupmessage_id", referencedColumnName: "groupmessage_id")]
    private GroupMessage $groupMessage;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate("user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\Column(name: "groupmessagelike_date", type: "datetime")]
    private ?DateTimeInterface $likeDate;

    #[ORM\Column(name: "groupmessagelike_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getGroupMessage(): GroupMessage
    {
        return $this->groupMessage;
    }

    public function setGroupMessage(GroupMessage $groupMessage): void
    {
        $this->groupMessage = $groupMessage;
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