<?php

namespace Descolar\Data\Entities\Institution;

use Descolar\Data\Repository\Institution\DiplomaRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: DiplomaRepository::class)]
#[ORM\Table(name: "diploma")]
#[Validate\Validate]
class Diploma
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "diploma_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Column(name: "diploma_name", type: "string", length: 100)]
    #[Validate\Validate(name: "name")]
    #[Validate\NotNull]
    #[Validate\Length(max: 100)]
    private string $name;

    #[ORM\Column(name:"diploma_isactive", type: "boolean")]
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