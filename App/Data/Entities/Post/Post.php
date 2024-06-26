<?php

namespace Descolar\Data\Entities\Post;

use DateTimeInterface;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Post\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[ORM\Table(name: "post")]
#[Validate\Validate]
class Post
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "post_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate(name: "user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\Column(name: "post_text_content", type: "string", length: 400, nullable: true)]
    #[Validate\Validate(name: "content")]
    #[Validate\Length(max: 400)]
    private ?string $content;

    #[ORM\Column(name: "post_location", type: "string", length: 255)]
    #[Validate\Validate(name: "location")]
    #[Validate\NotNull]
    #[Validate\Length(max: 255)]
    private string $location; // Post location (IP)

    #[ORM\Column(name: "post_date", type: "datetime")]
    #[Validate\Validate(name: "date")]
    #[Validate\NotNull]
    private DateTimeInterface $date;

    #[ORM\Column(name: "post_ispinned", type: "boolean")]
    private bool $isPinned = false;

    #[ORM\ManyToOne(targetEntity: Post::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "repost_id", referencedColumnName: "post_id")]
    private ?Post $repostedPost = null;

    #[ORM\Column(name: "post_isactive", type: "boolean")]
    private bool $isActive = true;

    #[ORM\JoinTable(name: 'link_postmedia')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'post_id')]
    #[ORM\InverseJoinColumn(name: 'media_id', referencedColumnName: 'media_id')]
    #[ORM\ManyToMany(targetEntity: Media::class)]
    private Collection $medias;

    public function __construct()
    {
    }

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setPinned(bool $isPinned): void
    {
        $this->isPinned = $isPinned;
    }

    public function getRepostedPost(): ?Post
    {
        return $this->repostedPost;
    }

    public function setRepostedPost(Post $repostedPost): void
    {
        $this->repostedPost = $repostedPost;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getMedias(): Collection
    {
        if(!isset($this->medias)) {
            $this->medias = new ArrayCollection();
        }
        return $this->medias;
    }

    public function addMedia(Media $media): void
    {
        $this->getMedias()->add($media);
    }
}