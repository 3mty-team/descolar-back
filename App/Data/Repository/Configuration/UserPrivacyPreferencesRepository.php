<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\App;
use Descolar\Data\Entities\Configuration\UserPrivacyPreferences;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\Types\Boolean;

class UserPrivacyPreferencesRepository extends EntityRepository
{
    public function getUserPrivacyPreferenceToJson(): array
    {
        $userPrivacyPreferences = $this->findOneBy(['user' => App::getUserUuid()]);

        return $this->UserPrivacyPreferenceToJson($userPrivacyPreferences);
    }

    public function createUserPrivacyPreference(String $sFeedVisibility, String $sSearchVisibility): array
    {
        if ($this->ConvertToBool($sFeedVisibility) === null || $this->ConvertToBool($sSearchVisibility) === null) {
            throw new EndpointException('Invalid parameters', 400);
        }

        $feedVisibility = $this->ConvertToBool($sFeedVisibility);
        $searchVisibility = $this->ConvertToBool($sSearchVisibility);

        $user = App::getOrmManager()->connect()->getRepository(User::class)->findOneBy(["uuid" => App::getUserUuid()]);
        if ($user === null) {
            throw new EndpointException('User not found', 404);
        }

        $alreadyExists = $this->findOneBy(['user' => $user]);
        if ($alreadyExists !== null) {
            throw new EndpointException('User privacy preference already exists', 400);
        }

        $userPrivacyPreferences = new UserPrivacyPreferences();
        $userPrivacyPreferences->setUser($user);
        $userPrivacyPreferences->setFeedVisibility($feedVisibility);
        $userPrivacyPreferences->setSearchVisibility($searchVisibility);

        $this->getEntityManager()->persist($userPrivacyPreferences);
        $this->getEntityManager()->flush();

        return $this->UserPrivacyPreferenceToJson($userPrivacyPreferences);
    }

    public function updateUserPrivacyPreference(?String $sFeedVisibility, ?String $sSearchVisibility): array
    {
        $userPrivacyPreferences = $this->findOneBy(['user' => App::getUserUuid()]);
        if ($userPrivacyPreferences === null) {
            throw new EndpointException('User privacy preference does not exist', 404);
        }

        if (!is_null($sFeedVisibility)) {
            if ($this->ConvertToBool($sFeedVisibility) === null) {
                throw new EndpointException('Invalid parameters', 400);
            }
            $feedVisibility = $this->ConvertToBool($sFeedVisibility);
            $userPrivacyPreferences->setFeedVisibility($feedVisibility);
        }

        if (!is_null($sSearchVisibility)) {
            if ($this->ConvertToBool($sSearchVisibility) === null) {
                throw new EndpointException('Invalid parameters', 400);
            }
            $searchVisibility = $this->ConvertToBool($sSearchVisibility);
            $userPrivacyPreferences->setSearchVisibility($searchVisibility);
        }

        $this->getEntityManager()->persist($userPrivacyPreferences);
        $this->getEntityManager()->flush();

        $this->UserPrivacyPreferenceToJson($userPrivacyPreferences);

        return $this->UserPrivacyPreferenceToJson($userPrivacyPreferences);
    }


    private function ConvertToBool(String $value): ?bool
    {
        if ($value === 'true' || $value === '1') {
            return true;
        } else if ($value === 'false' || $value === '0') {
            return false;
        }
        return null;
    }

    public function UserPrivacyPreferenceToJson($privacyPreferences): array
    {
        return [
            'feed_visibility' => $privacyPreferences->isFeedVisibility(),
            'search_visibility' => $privacyPreferences->isSearchVisibility()
        ];
    }
}