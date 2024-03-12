<?php

namespace Descolar\Data\Entities\Group;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Group\GroupMessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupMessageRepository::class)]
#[ORM\Table(name: "group_message")]
class GroupMessage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "groupmessage_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: "group_id", referencedColumnName: "post_id")]
    private Group $group;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "groupmessage_content", type: "string", length: 2000)]
    private string $content;

    #[ORM\Column(name: "groupmessage_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTimeInterface $date;

    #[ORM\Column(name: "groupmessage_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
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