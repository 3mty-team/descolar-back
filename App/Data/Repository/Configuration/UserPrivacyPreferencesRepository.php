<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\App;
use Descolar\Data\Entities\Configuration\UserPrivacyPreferences;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class UserPrivacyPreferencesRepository extends EntityRepository
{
    public function getUserPrivacyPreferenceToJson(): array
    {
        $loggedUser = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();
        $userPrivacyPreferences = $this->findOneBy(['user' => $loggedUser]);

        return $this->userPrivacyPreferenceToJson($userPrivacyPreferences);
    }

    public function createUserPrivacyPreference(?string $sFeedVisibility, ?string $sSearchVisibility): array
    {
        if ($this->convertToBool($sFeedVisibility) === null || $this->convertToBool($sSearchVisibility) === null) {
            throw new EndpointException('Invalid parameters', 400);
        }

        $feedVisibility = $this->convertToBool($sFeedVisibility);
        $searchVisibility = $this->convertToBool($sSearchVisibility);

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $alreadyExists = $this->findOneBy(['user' => $user]);
        if ($alreadyExists !== null) {
            throw new EndpointException('User privacy preference already exists', 400);
        }

        $userPrivacyPreferences = new UserPrivacyPreferences();
        $userPrivacyPreferences->setUser($user);
        $userPrivacyPreferences->setFeedVisibility($feedVisibility);
        $userPrivacyPreferences->setSearchVisibility($searchVisibility);

        Validator::getInstance($userPrivacyPreferences)->check();

        $this->getEntityManager()->persist($userPrivacyPreferences);
        $this->getEntityManager()->flush();

        return $this->userPrivacyPreferenceToJson($userPrivacyPreferences);
    }

    public function updateUserPrivacyPreference(?string $sFeedVisibility, ?string $sSearchVisibility): array
    {
        $loggedUser = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();
        $userPrivacyPreferences = $this->findOneBy(['user' => $loggedUser]);
        if ($userPrivacyPreferences === null) {
            throw new EndpointException('User privacy preference does not exist', 404);
        }

        if (!is_null($sFeedVisibility)) {
            if ($this->convertToBool($sFeedVisibility) === null) {
                throw new EndpointException('Invalid parameters', 400);
            }
            $feedVisibility = $this->convertToBool($sFeedVisibility);
            $userPrivacyPreferences->setFeedVisibility($feedVisibility);
        }

        if (!is_null($sSearchVisibility)) {
            if ($this->convertToBool($sSearchVisibility) === null) {
                throw new EndpointException('Invalid parameters', 400);
            }
            $searchVisibility = $this->convertToBool($sSearchVisibility);
            $userPrivacyPreferences->setSearchVisibility($searchVisibility);
        }

        $this->getEntityManager()->persist($userPrivacyPreferences);
        $this->getEntityManager()->flush();

        return $this->userPrivacyPreferenceToJson($userPrivacyPreferences);
    }


    private function convertToBool(string $value): ?bool
    {
        if ($value === 'true' || $value === '1') {
            return true;
        } else if ($value === 'false' || $value === '0') {
            return false;
        }
        return null;
    }

    public function userPrivacyPreferenceToJson(UserPrivacyPreferences $privacyPreferences): array
    {
        return [
            'feed_visibility' => $privacyPreferences->isFeedVisibility(),
            'search_visibility' => $privacyPreferences->isSearchVisibility()
        ];
    }
}