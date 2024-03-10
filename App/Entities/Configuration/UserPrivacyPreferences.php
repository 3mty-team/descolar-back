<?php

namespace Descolar\Entities\Configuration;

use Descolar\Entities\User\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "param_user_privacy_preferences")]
class UserPrivacyPreferences
{

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
    private int $userId;

    #[ORM\Column(name: "feed_visibility", type: "boolean", options: ["default" => 1])]
    private bool $feedVisibility;

    #[ORM\Column(name:"search_visibility", type: "boolean", options: ["default" => 1])]
    private bool $searchVisibility;

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
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