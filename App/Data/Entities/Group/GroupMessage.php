<?php

namespace Descolar\Data\Entities\Group;

use DateTimeInterface;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Group\GroupMessageRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: GroupMessageRepository::class)]
#[ORM\Table(name: "group_message")]
#[Validate\Validate]
class GroupMessage
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "groupmessage_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Group::class)]
    #[ORM\JoinColumn(name: "group_id", referencedColumnName: "group_id")]
    #[Validate\Validate("group")]
    #[Validate\NotNull]
    private Group $group;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate("user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\Column(name: "groupmessage_content", type: "string", length: 2000)]
    #[Validate\Validate("content")]
    #[Validate\NotNull]
    #[Validate\Length(max: 2000)]
    private string $content;

    #[ORM\Column(name: "groupmessage_date", type: "datetime")]
    #[Validate\Validate("date")]
    #[Validate\NotNull]
    private DateTimeInterface $date;

    #[ORM\Column(name: "groupmessage_isactive", type: "boolean")]
    private bool $isActive = true;

    #[ORM\JoinTable(name: 'link_groupmessagemedia')]
    #[ORM\JoinColumn(name: 'groupmessage_id', referencedColumnName: 'groupmessage_id')]
    #[ORM\InverseJoinColumn(name: 'media_id', referencedColumnName: 'media_id')]
    #[ORM\ManyToMany(targetEntity: Media::class)]
    private Collection $medias;

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

    public function getMedias(): Collection
    {
        if(!isset($this->medias)) {
            $this->medias = new ArrayCollection();
        }
        return $this->medias;
    }

    public function setMedias(Collection $medias): void
    {
        $this->medias = $medias;
    }

    public function addMedia(Media $media): void
    {
        $this->getMedias()->add($media);
    }
}