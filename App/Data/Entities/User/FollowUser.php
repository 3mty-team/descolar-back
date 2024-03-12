<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Data\Repository\User\FollowUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FollowUserRepository::class)]
#[ORM\Table(name: "user_follow")]
class FollowUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_follower_id", referencedColumnName: "user_id")]
    private User $follower; # A, the person following B

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_following_id", referencedColumnName: "user_id")]
    private User $following; # B, the person being followed by A

    #[ORM\Column(name: "userfollow_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "userfollow_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getFollower(): User
    {
        return $this->follower;
    }

    public function setFollower(User $follower): void
    {
        $this->follower = $follower;
    }

    public function getFollowingId(): User
    {
        return $this->following;
    }

    public function setFollowingId(User $following): void
    {
        $this->following = $following;
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