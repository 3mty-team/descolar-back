<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Data\Repository\User\FollowUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: FollowUserRepository::class)]
#[ORM\Table(name: "user_follow")]
#[Validate\Validate]
class FollowUser
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_follower_id", referencedColumnName: "user_id")]
    #[Validate\Validate("follower")]
    #[Validate\NotNull]
    private User $follower; # A, the person following B

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_following_id", referencedColumnName: "user_id")]
    #[Validate\Validate("following")]
    #[Validate\NotNull]
    private User $following; # B, the person being followed by A

    #[ORM\Column(name: "userfollow_date", type: "datetime")]
    #[Validate\Validate("date")]
    #[Validate\NotNull]
    private DateTimeInterface $date;

    #[ORM\Column(name: "userfollow_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getFollower(): User
    {
        return $this->follower;
    }

    public function setFollower(User $follower): void
    {
        $this->follower = $follower;
    }

    public function getFollowing(): User
    {
        return $this->following;
    }

    public function setFollowing(User $following): void
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