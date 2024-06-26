<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Adapters\Orm\Generator\UUIDGenerator;
use Descolar\Data\Entities\Institution\Formation;
use Descolar\Data\Repository\User\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "user")]
#[Validate\Validate]
class User
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UUIDGenerator::class)]
    #[ORM\Column(name: "user_id", type: "guid")]
    private string $uuid;

    #[ORM\Column(name: "user_username", type: "string", length: 20, unique: true)]
    #[Validate\Validate("username")]
    #[Validate\NotNull]
    #[Validate\Length(max: 20)]
    #[Validate\Unique(clazzEntity: User::class, fieldEntity: "username")]
    #[Validate\Regex(regex: "^[a-zA-Z0-9_-]+$")]
    private string $username;

    #[ORM\Column(name: "user_profilepicturepath", type: "string", length: 200, nullable: true)]
    #[Validate\Validate("pfpPath")]
    #[Validate\Length(max: 200)]
    private ?string $pfpPath = null;

    #[ORM\Column(name: "user_bannerpath", type: "string", length: 200, nullable: true)]
    #[Validate\Validate("bannerPath")]
    #[Validate\Length(max: 200)]
    private ?string $bannerPath = null;

    #[ORM\Column(name: "user_firstname", type: "string", length: 100)]
    #[Validate\Validate("firstname")]
    #[Validate\NotNull]
    #[Validate\Length(max: 100)]
    private string $firstname;

    #[ORM\Column(name: "user_lastname", type: "string", length: 50)]
    #[Validate\Validate("lastname")]
    #[Validate\NotNull]
    #[Validate\Length(max: 50)]
    private string $lastname;

    #[ORM\Column(name: "user_mail", type: "string", length: 255, unique: true)]
    #[Validate\Validate("mail")]
    #[Validate\NotNull]
    #[Validate\Length(max: 255)]
    #[Validate\Unique(clazzEntity: User::class, fieldEntity: "mail")]
    #[Validate\PatternMatch(endWith: "@etu.u-paris.fr")]
    private string $mail;

    #[ORM\Column(name: "user_account_verify", type: "string", length: 255, nullable: true)]
    #[Validate\Validate("token")]
    #[Validate\Length(max: 255)]
    private ?string $token = null;

    #[ORM\Column(name: "user_dateofbirth", type: "date", nullable: true)]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "user_biography", type: "string", length: 100, nullable: true)]
    #[Validate\Validate("biography")]
    #[Validate\Length(max: 100)]
    private ?string $biography = null;

    #[ORM\ManyToOne(targetEntity: Formation::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_formation", referencedColumnName: "formation_id")]
    private ?Formation $formation = null;

    #[ORM\Column(name: "user_isactive", type: "boolean")]
    private bool $isActive = true;

    public function __construct()
    {
    }

    public function getUUID(): string
    {
        return $this->uuid;
    }

    public function setUUID(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getProfilePicturePath(): ?string
    {
        return $this->pfpPath;
    }

    public function setProfilePicturePath(?string $pfpPath): void
    {
        $this->pfpPath = $pfpPath;
    }

    public function getBannerPath(): ?string
    {
        return $this->bannerPath;
    }

    public function setBannerPath(?string $bannerPath): void
    {
        $this->bannerPath = $bannerPath;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): void
    {
        $this->lastname = $lastname;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): void
    {
        $this->formation = $formation;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): void
    {
        $this->biography = $biography;
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