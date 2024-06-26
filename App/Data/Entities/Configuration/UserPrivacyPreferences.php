<?php

namespace Descolar\Data\Entities\Configuration;

use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Configuration\UserPrivacyPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;
use Descolar\Adapters\Validator\Annotations as Validate;

#[ORM\Entity(repositoryClass: UserPrivacyPreferencesRepository::class)]
#[ORM\Table(name: "param_user_privacy_preferences")]
#[Validate\Validate]
class UserPrivacyPreferences
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    #[Validate\Validate("user")]
    #[Validate\NotNull]
    private User $user;

    #[ORM\Column(name: "feed_visibility", type: "boolean")]
    private bool $feedVisibility = true;

    #[ORM\Column(name:"search_visibility", type: "boolean")]
    private bool $searchVisibility = true;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function isFeedVisibility(): bool
    {
        return $this->feedVisibility;
    }

    public function setFeedVisibility(bool $feedVisibility): void
    {
        $this->feedVisibility = $feedVisibility;
    }

    public function isSearchVisibility(): bool
    {
        return $this->searchVisibility;
    }

    public function setSearchVisibility(bool $searchVisibility): void
    {
        $this->searchVisibility = $searchVisibility;
    }
}