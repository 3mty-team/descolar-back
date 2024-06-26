<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\Configuration\Theme;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;

class ThemeRepository extends EntityRepository
{
    public function getAllThemesToJson(): array
    {
        /*
         * @var Theme[] $themes
         */
        $themes = $this->findAll();
        $themesArray = [];

        foreach ($themes as $theme) {
            $themesArray[] = $this->toJson($theme);
        }

        return $themesArray;
    }

    public function getThemeById(?int $themeId): Theme
    {
        $theme = $this->find($themeId);

        if ($theme === null) {
            throw new EndpointException('Theme not found', 404);
        }

        return $theme;
    }

    public function toJson(Theme $theme): array
    {
        return [
            'id' => $theme->getId(),
            'name' => $theme->getName(),
            'is_active' => $theme->isActive()
        ];
    }
}