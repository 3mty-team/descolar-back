<?php

namespace Descolar\Data\Entities\Post;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Post\PostCommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: PostCommentRepository::class)]
#[ORM\Table(name: "post_comment")]
#[Validate\Validate]
class PostComment
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "postcomment_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Post::class)]
    #[ORM\JoinColumn(name: "post_id", referencedColumnName: "post_id")]
    #[Validate\Validate(name: "post")]
    #[Validate\NotNull]
    private Post $post;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate(name: "user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\Column(name: "postcomment_content", type: "string", length: 200)]
    #[Validate\Validate(name: "content")]
    #[Validate\NotNull]
    #[Validate\Length(max: 200)]
    private string $content;

    #[ORM\Column(name: "postcomment_date", type: "datetime")]
    private DateTimeInterface $date;

    #[ORM\Column(name: "postcomment_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

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