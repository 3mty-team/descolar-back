<?php

namespace Descolar\Entities\User;

use DateTimeInterface;
use Descolar\Entities\Report\ReportCategory;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user_search_history")]
class SearchHistoryUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "usersearchhistory_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private int $userId;

    #[ORM\Column(name: "usersearchhistory_search", type: "string", length: 200)]
    private string $search;

    #[ORM\Column(name: "usersearchhistory_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "usersearchhistory_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function setSearch(string $search): void
    {
        $this->search = $search;
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