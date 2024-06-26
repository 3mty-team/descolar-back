<?php

namespace Descolar\Data\Entities\Report;

use DateTimeInterface;
use Descolar\Data\Entities\User\MessageUser;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Report\MessageReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: MessageReportRepository::class)]
#[ORM\Table(name: "message_report")]
#[Validate\Validate]
class MessageReport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "messagereport_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: MessageUser::class)]
    #[ORM\JoinColumn(name: "message_id", referencedColumnName: "message_id")]
    #[Validate\Validate("message")]
    #[Validate\NotNull]
    private MessageUser $message;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate("reporter")]
    #[Validate\NotNull]
    private User $reporter;

    #[ORM\ManyToOne(targetEntity: ReportCategory::class)]
    #[ORM\JoinColumn(name: "reportcategory_id", referencedColumnName: "reportcategory_id")]
    #[Validate\Validate("reportCategory")]
    #[Validate\NotNull]
    private ReportCategory $reportCategory;

    #[ORM\Column(name: "messagereport_comment", type: "string", length: 100, nullable: true)]
    #[Validate\Validate("comment")]
    #[Validate\Length(max: 100)]
    private ?string $comment = null;

    #[ORM\Column(name: "messagereport_date", type: "datetime")]
    #[Validate\Validate("date")]
    #[Validate\NotNull]
    private DateTimeInterface $date;

    #[ORM\Column(name: "messagereport_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getMessage(): MessageUser
    {
        return $this->message;
    }

    public function setMessage(MessageUser $message): void
    {
        $this->message = $message;
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

    public function getDate(): DateTimeInterface
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