<?php

namespace Descolar\Data\Entities\Configuration;

use Descolar\Data\Repository\Configuration\ThemeRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[ORM\Table(name: "theme")]
#[Validate\Validate]
class Theme
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "theme_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Column(name: "theme_name", type: "string", length: 30)]
    #[Validate\Validate("name")]
    #[Validate\NotNull]
    #[Validate\Length(max: 30)]
    private string $name;

    #[ORM\Column(name:"theme_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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