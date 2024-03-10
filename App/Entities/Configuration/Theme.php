<?php

namespace Descolar\Entities\Configuration;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "theme")]
class Theme
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "theme_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Column(name: "theme_name", type: "string", length: 30)]
    private string $name;

    #[ORM\Column(name:"theme_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

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