<?php

namespace Descolar\Entities\User;

use DateTimeInterface;
use Descolar\Entities\Institution\Formation;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "user")]
class User
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "user_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "user_username", type: "string", length: 20, unique: true)]
    private string $username;

    #[ORM\Column(name: "user_profilepicturepath", type: "string", length: 200, nullable: true)]
    private ?string $profile;

    #[ORM\Column(name: "user_firstname", type: "string", length: 100)]
    private string $firstname;

    #[ORM\Column(name: "user_lastname", type: "string", length: 50)]
    private string $lastname;

    #[ORM\Column(name: "user_mail", type: "string", length: 255, unique: true)]
    private string $mail;

    #[ORM\Column(name: "user_dateofbirth", type: "date", nullable: true)]
    private ?DateTimeInterface $date;

    #[ORM\ManyToOne(targetEntity: Formation::class, fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_formation", referencedColumnName: "formation_id")]
    private ?Formation $formation;

    #[ORM\Column(name: "user_biography", type: "string", length: 100, nullable: true)]
    private ?string $biography;

    #[ORM\Column(name: "user_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): void
    {
        $this->profile = $profile;
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