<?php

namespace Descolar\Data\Entities\Configuration;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Configuration\SessionRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\Table(name: "session")]
#[Validate\Validate]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "session_id", type: "integer", length: 11, unique: true)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate("user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\Column(name: "session_date", type: "datetime")]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "session_localisation", type: "string", length: 255)]
    #[Validate\Validate("localisation")]
    #[Validate\NotNull]
    #[Validate\Length(max: 255)]
    private string $localisation;

    #[ORM\Column(name: "session_useragent", type: "string", length: 200)]
    #[Validate\Validate("userAgent")]
    #[Validate\NotNull]
    #[Validate\Length(max: 200)]
    private string $userAgent;

    #[ORM\Column(name: "session_isactive", type: "boolean")]
    private bool $isActive = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
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