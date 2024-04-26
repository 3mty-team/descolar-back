<?php

namespace Descolar\Data\Entities\Report;

use DateTimeInterface;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Report\GroupMessageReportRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupMessageReportRepository::class)]
#[ORM\Table(name: "groupmessage_report")]
class GroupMessageReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "groupmessagereport_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: GroupMessage::class)]
    #[ORM\JoinColumn(name: "groupmessage_id", referencedColumnName: "groupmessage_id")]
    private GroupMessage $groupMessage;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $reporter;

    #[ORM\ManyToOne(targetEntity: ReportCategory::class)]
    #[ORM\JoinColumn(name: "reportcategory_id", referencedColumnName: "reportcategory_id")]
    private ReportCategory $reportCategory;

    #[ORM\Column(name: "groupmessagereport_comment", type: "string", length: 100, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(name: "groupmessagereport_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTimeInterface $date;

    #[ORM\Column(name: "groupmessagereport_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getGroupMessage(): GroupMessage
    {
        return $this->groupMessage;
    }

    public function setGroupMessage(GroupMessage $groupMessage): void
    {
        $this->groupMessage = $groupMessage;
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

    public function getComment(): ?string
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
}