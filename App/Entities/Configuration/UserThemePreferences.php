<?php

namespace Descolar\Entities\Configuration;

use Descolar\Entities\User\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "param_user_theme_preferences")]
class UserThemePreferences
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private int $userId;

    #[ORM\ManyToOne(targetEntity: Theme::class)]
    #[ORM\JoinColumn(name: "theme_id", referencedColumnName: "theme_id")]
    private int $themeId;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getThemeId(): int
    {
        return $this->themeId;
    }

    public function setThemeId(int $themeId): void
    {
        $this->themeId = $themeId;
    }

}

