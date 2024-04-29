<?php

namespace Descolar\Data\Entities\Media;

use Descolar\Data\Repository\Media\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ORM\Table(name: "media")]
class Media
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "media_id", type: "integer", length: 11, unique: true)]
    private int $id;

    #[ORM\Column(name: "media_path", type: "string", length: 200)]
    private string $path = "";

    #[ORM\Column(name: "media_type", type: "string", enumType: MediaType::class)]
    private ?MediaType $mediaType;

    #[ORM\Column(name: "media_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getMediaType(): MediaType
    {
        return $this->mediaType;
    }

    public function setMediaType(?MediaType $mediaType): void
    {
        $this->mediaType = $mediaType;
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