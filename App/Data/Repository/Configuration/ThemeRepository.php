<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\Configuration\Theme;
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

    public function getOneThemeToJson(int $themeId): array
    {
        $theme = $this->find($themeId);
        if ($theme === null) {
            return [];
        }

        return $this->toJson($theme);
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