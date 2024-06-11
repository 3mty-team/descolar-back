<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\App;
use Descolar\Data\Entities\Configuration\Theme;
use Descolar\Data\Entities\Configuration\UserThemePreferences;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class UserThemePreferencesRepository extends EntityRepository
{
    public function getThemePreferenceToJson(): array
    {

        $theme = $this->findOneBy(["user" => App::getUserUuid()])->getTheme();
        if ($theme === null) {
            throw new EndpointException('User theme preference does not exist', 404);
        }

        return OrmConnector::getInstance()->getRepository(Theme::class)->toJson($theme);
    }
    public function createThemePreference($themeId): ?Theme
    {

        if (empty($themeId)) {
            throw new EndpointException('Missing parameters', 400);
        }

        if (!is_numeric($themeId)) {
            throw new EndpointException('Invalid parameters', 400);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $alreadyExists = $this->findOneBy(['user' => $user]);
        if ($alreadyExists !== null) {
            throw new EndpointException('User theme preference already exists', 400);
        }

        $theme = OrmConnector::getInstance()->getRepository(Theme::class)->findOneBy(["id" => $themeId]);
        if ($theme === null) {
            throw new EndpointException('Theme not found', 404);
        }

        $userThemePreference = new UserThemePreferences();
        $userThemePreference->setUser($user);
        $userThemePreference->setTheme($theme);

        $this->getEntityManager()->persist($userThemePreference);
        $this->getEntityManager()->flush();

        return $theme;
    }

    public function updateThemePreference($themeId): ?Theme
    {
        $userThemePreference = $this->findOneBy(["user" => App::getUserUuid()]);
        if ($userThemePreference === null) {
            throw new EndpointException('User theme preference does not exist', 404);
        }

        $theme = OrmConnector::getInstance()->getRepository(Theme::class)->findOneBy(["id" => $themeId]);
        if ($theme === null) {
            throw new EndpointException('Theme not found', 404);
        }

        $userThemePreference->setTheme($theme);

        $this->getEntityManager()->persist($userThemePreference);
        $this->getEntityManager()->flush();

        return $theme;
    }
}