<?php

namespace Descolar\Data\Entities\User;

use DateTimeInterface;
use Descolar\Adapters\Orm\Generator\UUIDGenerator;
use Descolar\Data\Repository\User\UserRepository;
use Descolar\Data\Entities\Institution\Formation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "user")]
class User
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UUIDGenerator::class)]
    #[ORM\Column(name: "user_id", type: "guid")]
    private string $uuid;

    #[ORM\Column(name: "user_username", type: "string", length: 20, unique: true)]
    private string $username;

    #[ORM\Column(name: "user_profilepicturepath", type: "string", length: 200, nullable: true)]
    private ?string $pfpPath;

    #[ORM\Column(name: "user_firstname", type: "string", length: 100)]
    private string $firstname;

    #[ORM\Column(name: "user_lastname", type: "string", length: 50)]
    private string $lastname;

    #[ORM\Column(name: "user_mail", type: "string", length: 255, unique: true)]
    private string $mail;

    #[ORM\Column(name: "user_account_verify", type: "string", length: 255, nullable: true)]
    private ?string $token;

    #[ORM\Column(name: "user_dateofbirth", type: "date", nullable: true)]
    private ?DateTimeInterface $date;

    #[ORM\Column(name: "user_biography", type: "string", length: 100, nullable: true)]
    private ?string $biography;

    #[ORM\ManyToOne(targetEntity: Formation::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_formation", referencedColumnName: "formation_id")]
    private ?Formation $formation;

    #[ORM\Column(name: "user_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

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

    public function getToken(): string
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