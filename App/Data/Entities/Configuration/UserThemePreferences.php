<?php

namespace Descolar\Data\Entities\Configuration;

use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Configuration\UserThemePreferencesRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: UserThemePreferencesRepository::class)]
#[ORM\Table(name: "param_user_theme_preferences")]
#[Validate\Validate]
class UserThemePreferences
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate("user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Theme::class)]
    #[ORM\JoinColumn(name: "theme_id", referencedColumnName: "theme_id")]
    #[Validate\Validate("theme")]
    #[Validate\NotNull]
    private Theme $theme;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function setTheme(Theme $theme): void
    {
        $this->theme = $theme;
    }
}

