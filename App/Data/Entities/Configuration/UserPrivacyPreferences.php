<?php

namespace Descolar\Data\Entities\Configuration;

use Descolar\Data\Entities\User\User;
use Descolar\Data\Repository\Configuration\UserPrivacyPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPrivacyPreferencesRepository::class)]
#[ORM\Table(name: "param_user_privacy_preferences")]
class UserPrivacyPreferences
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private User $user;

    #[ORM\Column(name: "feed_visibility", type: "boolean", options: ["default" => 1])]
    private bool $feedVisibility;

    #[ORM\Column(name:"search_visibility", type: "boolean", options: ["default" => 1])]
    private bool $searchVisibility;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUserId(User $user): void
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