<?php

namespace Descolar\Data\Entities\Configuration;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Configuration\SessionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: "session")]
class Session
{
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "session_id", type: "integer", length: 11, unique: true)]
    private int $id;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private int $userId;

    #[ORM\Id]
    #[ORM\Column(name: "session_date", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?DateTimeInterface $date;

    #[ORM\Id]
    #[ORM\Column(name: "session_localisation", type: "string", length: 255)]
    private string $localisation;

    #[ORM\Id]
    #[ORM\Column(name: "session_useragent", type: "string", length: 200)]
    private string $userAgent;

    #[ORM\Column(name: "session_isactive", type: "boolean", options: ["default" => 1])]
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

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getLocalisation(): string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): void
    {
        $this->localisation = $localisation;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
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