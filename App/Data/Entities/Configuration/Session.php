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
    #[ORM\Column(name: "session_localisation", type: "//TODO find inet4 equivalent")]
    private Object $localisation;

    #[ORM\Id]
    #[ORM\Column(name: "session_useragent", type: "string", length: 200)]
    private string $userAgent;

    #[ORM\Column(name: "session_isactive", type: "boolean", options: ["default" => 1])]
    private bool $isActive;

    //TODO generate getters and setters
}