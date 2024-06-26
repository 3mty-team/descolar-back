<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Repository\User\MessageUserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageUserRepository::class)]
#[ORM\Table(name: "message")]
class MessageUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "message_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_sender_id", referencedColumnName: "user_id")]
    private User $sender; # A, the person sending a message to B

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_receiver_id", referencedColumnName: "user_id")]
    private User $receiver; # B, the person receiving a message from A

    #[ORM\Column(name: "message_content", type: "string", length: 2000)]
    private string $content;

    #[ORM\Column(name: "message_islikedbysender", type: "boolean", options: ["default" => 0])]
    private bool $isLikedBySender;

    #[ORM\Column(name: "message_islikedbyreceiver", type: "boolean", options: ["default" => 0])]
    private bool $isLikedByReceiver;

    #[ORM\Column(name: "message_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "message_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    #[ORM\JoinTable(name: 'link_messagemedia')]
    #[ORM\JoinColumn(name: 'message_id', referencedColumnName: 'message_id')]
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

    public function getSender(): User
    {
        return $this->sender;
    }

    public function setSender(User $sender): void
    {
        $this->sender = $sender;
    }

    public function getReceiver(): User
    {
        return $this->receiver;
    }

    public function setReceiver(User $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isLikedBySender(): bool
    {
        return $this->isLikedBySender;
    }

    public function setIsLikedBySender(bool $isLikedBySender): void
    {
        $this->isLikedBySender = $isLikedBySender;
    }

    public function isLikedByReceiver(): bool
    {
        return $this->isLikedByReceiver;
    }

    public function setIsLikedByReceiver(bool $isLikedByReceiver): void
    {
        $this->isLikedByReceiver = $isLikedByReceiver;
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