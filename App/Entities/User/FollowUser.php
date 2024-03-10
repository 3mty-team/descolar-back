<?php

namespace Descolar\Entities\User;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_follow")]
class FollowUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_follower_id", referencedColumnName: "user_id")]
    private int $followerId; # A, the person following B

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_following_id", referencedColumnName: "user_id")]
    private int $followingId; # B, the person being followed by A

    #[ORM\Column(name: "userfollow_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "userfollow_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getFollowerId(): int
    {
        return $this->followerId;
    }

    public function setFollowerId(int $followerId): void
    {
        $this->followerId = $followerId;
    }

    public function getFollowingId(): int
    {
        return $this->followingId;
    }

    public function setFollowingId(int $followingId): void
    {
        $this->followingId = $followingId;
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