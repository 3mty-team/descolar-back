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

    public function getThemePreferenceToJson(): array
    {

        $loggedUser = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $theme = $this->findOneBy(["user" => $loggedUser])->getTheme();
        if ($theme === null) {
            throw new EndpointException('User theme preference does not exist', 404);
        }

        return OrmConnector::getInstance()->getRepository(Theme::class)->toJson($theme);
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

        $userThemePreference = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $theme = $this->getThemeById($themeId);

        $userThemePreference->setTheme($theme);

        Validator::getInstance($userThemePreference)->check();

        OrmConnector::getInstance()->persist($userThemePreference);
        OrmConnector::getInstance()->flush();

        return $theme;
    }
}