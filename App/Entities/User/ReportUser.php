<?php

namespace Descolar\Entities\User;

use DateTimeInterface;
use Descolar\Entities\Report\ReportCategory;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_report")]
class ReportUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "userreport_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_reported_id", referencedColumnName: "user_id")]
    private int $reportedId; # A, the person being reported

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_reporting_id", referencedColumnName: "user_id")]
    private int $reporterId; # B, the person reporting A

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ReportCategory::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "reportcategory_id", referencedColumnName: "reportcategory_id")]
    private int $reportCategory;

    #[ORM\Column(name: "userreport_comment", type: "string", length: 100, nullable: true)]
    private string $comment;

    #[ORM\Column(name: "userreport_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "userreport_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getReportedId(): int
    {
        return $this->reportedId;
    }

    public function setReportedId(int $reportedId): void
    {
        $this->reportedId = $reportedId;
    }

    public function getReporterId(): int
    {
        return $this->reporterId;
    }

    public function setReporterId(int $reporterId): void
    {
        $this->reporterId = $reporterId;
    }

    public function getReportCategory(): int
    {
        return $this->reportCategory;
    }

    public function setReportCategory(int $reportCategory): void
    {
        $this->reportCategory = $reportCategory;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
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
}