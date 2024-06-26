<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\App;
use Descolar\Data\Entities\Configuration\Theme;
use Descolar\Data\Entities\Configuration\UserThemePreferences;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class UserThemePreferencesRepository extends EntityRepository
{

    public function getThemeById($themeId): ?Theme
    {
        $theme = OrmConnector::getInstance()->getRepository(Theme::class)->find($themeId);
        if ($theme === null) {
            throw new EndpointException('Theme does not exist', 404);
        }

        return $theme;
    }

    public function getThemePreferencesFromLoggedUser(): UserThemePreferences
    {
        $loggedUser = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $theme = $this->findOneBy(["user" => $loggedUser]);
        if ($theme === null) {
            throw new EndpointException('User theme preference does not exist', 404);
        }

        return $theme;
    }

    public function getThemePreferenceToJson(): array
    {
        $themePreferences = $this->getThemePreferencesFromLoggedUser();

        return OrmConnector::getInstance()->getRepository(Theme::class)->toJson($themePreferences->getTheme());
    }
    public function createThemePreference(?int $themeId): ?Theme
    {

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $alreadyExists = $this->findOneBy(['user' => $user]);
        if ($alreadyExists !== null) {
            throw new EndpointException('User theme preference already exists', 400);
        }

        $theme = OrmConnector::getInstance()->getRepository(Theme::class)->getThemeById($themeId);

        $userThemePreference = new UserThemePreferences();
        $userThemePreference->setUser($user);
        $userThemePreference->setTheme($theme);

        Validator::getInstance($userThemePreference)->check();

        OrmConnector::getInstance()->persist($userThemePreference);
        OrmConnector::getInstance()->flush();

        return $theme;
    }

    public function updateThemePreference($themeId): ?Theme
    {

        $userThemePreference = $this->getThemePreferencesFromLoggedUser();
        $theme = OrmConnector::getInstance()->getRepository(Theme::class)->getThemeById($themeId);

        $userThemePreference->setTheme($theme);

        Validator::getInstance($userThemePreference)->check();

        OrmConnector::getInstance()->persist($userThemePreference);
        OrmConnector::getInstance()->flush();

        return $theme;
    }
}