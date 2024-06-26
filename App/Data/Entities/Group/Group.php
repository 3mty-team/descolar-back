<?php

namespace Descolar\Data\Entities\Group;

use DateTimeInterface;
use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Group\GroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: "`group`")]
#[Validate\Validate]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "group_id", type: "integer", length: 11)]
    private int $id;

    #[ORM\Column(name: "group_name", type: "string", length: 150)]
    #[Validate\Validate("name")]
    #[Validate\NotNull]
    #[Validate\Length(max: 150)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "group_admin", referencedColumnName: "user_id")]
    #[Validate\Validate("admin")]
    #[Validate\NotNull]
    private User $admin;

    #[ORM\Column(name: "group_creationdate", type: "datetime")]
    private ?DateTimeInterface $creationDate;

    #[ORM\Column(name: "group_isactive", type: "boolean")]
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

    public function getAdmin(): User
    {
        return $this->admin;
    }

    public function setAdmin(User $admin): void
    {
        $this->admin = $admin;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?DateTimeInterface $creationDate): void
    {
        $this->creationDate = $creationDate;
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