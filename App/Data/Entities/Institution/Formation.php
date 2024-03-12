<?php

namespace Descolar\Data\Entities\Institution;
use Descolar\Data\Repository\Institution\FormationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
#[ORM\Table(name: "formation")]
class Formation
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "formation_id", type: "integer", length: 11)]
    private int $id;


    #[ORM\ManyToOne(targetEntity: Diploma::class)]
    #[ORM\JoinColumn(name: "diploma_id", referencedColumnName: "diploma_id")]
    private Diploma $diploma;

    #[ORM\Column(name: "formation_name", type: "string", length: 100)]
    private string $name;

    #[ORM\Column(name: "formation_shortname", type: "string", length: 10)]
    private string $shortName;

    #[ORM\Column(name: "formation_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDiploma(): Diploma
    {
        return $this->diploma;
    }

    public function setDiploma(Diploma $diploma): void
    {
        $this->diploma = $diploma;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
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