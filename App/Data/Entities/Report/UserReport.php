<?php

namespace Descolar\Data\Entities\Report;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Report\UserReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserReportRepository::class)]
#[ORM\Table(name: "user_report")]
class UserReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "userreport_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_reported_id", referencedColumnName: "user_id")]
    private User $reported; # A, the person being reported

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_reporting_id", referencedColumnName: "user_id")]
    private User $reporter; # B, the person reporting A

    #[ORM\ManyToOne(targetEntity: ReportCategory::class)]
    #[ORM\JoinColumn(name: "reportcategory_id", referencedColumnName: "reportcategory_id")]
    private ReportCategory $reportCategory;

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

    public function getReported(): User
    {
        return $this->reported;
    }

    public function setReported(User $reported): void
    {
        $this->reported = $reported;
    }

    public function getReporter(): User
    {
        return $this->reporter;
    }

    public function setReporter(User $reporter): void
    {
        $this->reporter = $reporter;
    }

    public function getReportCategory(): ReportCategory
    {
        return $this->reportCategory;
    }

    public function setReportCategory(ReportCategory $reportCategory): void
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