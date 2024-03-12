<?php

namespace Descolar\Data\Entities\Report;

use Descolar\Data\Repository\Report\ReportCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportCategoryRepository::class)]
#[ORM\Table(name: "report_category")]
class ReportCategory
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "reportcategory_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Column(name: "reportcategory_name", type: "string", length: 60)]
    private string $name;

    #[ORM\Column(name:"reportcategory_isactive", type: "boolean", options: ["default" => 1])]
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