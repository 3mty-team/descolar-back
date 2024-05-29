<?php

namespace Descolar\Data\Entities\Post;

use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Post\PostHiddenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostHiddenRepository::class)]
#[ORM\Table(name: "hidden_post")]
class PostHidden
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "post_id")]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "hiddenpost_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }
}