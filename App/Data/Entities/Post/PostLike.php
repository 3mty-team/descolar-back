<?php

namespace Descolar\Data\Entities\Post;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Post\PostLikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: PostLikeRepository::class)]
#[ORM\Table(name: "post_like")]
#[Validate\Validate]
class PostLike
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "post_id")]
    private Post $post;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "postlike_date", type: "datetime")]
    #[Validate\Validate(name: "date")]
    #[Validate\NotNull]
    private DateTimeInterface $date;

    #[ORM\Column(name: "postlike_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): void
    {
        $this->post = $post;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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