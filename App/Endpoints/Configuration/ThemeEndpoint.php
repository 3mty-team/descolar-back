<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Session;
use Descolar\Data\Entities\Configuration\Theme;
use Descolar\Data\Entities\Configuration\UserThemePreferences;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;

class ThemeEndpoint extends AbstractEndpoint
{
    #[Get('/config/themes', name: 'Retrieve all Themes', auth: true)]
    #[OA\Get(path: "/config/themes", summary: "Retrieve all Themes", tags: ["Configuration"])]
    #[Oa\Response(response: 200, description: 'All themes retrieved')]
    private function getAllThemes(): void
    {
        $themes = App::getOrmManager()->connect()->getRepository(Theme::class)->getAllThemesToJson();

        JsonBuilder::build()
            ->setCode(200)
            ->addData('message', 'All themes retrieved')
            ->addData('themes', $themes)
            ->getResult();
    }

    #[Get('/config/theme', name: 'Retrieve Theme preference', auth: true)]
    #[OA\Get(path: '/config/theme', summary: 'Retrieve Theme preference', tags: ['Configuration'])]
    #[OA\Response(response: 200, description: 'Theme preference retrieved')]
    private function getThemePreference(): void
    {
        $theme = App::getOrmManager()->connect()->getRepository(UserThemePreferences::class)->getThemePreferenceToJson();

        JsonBuilder::build()
            ->setCode(200)
            ->addData('message', 'Theme preference retrieved')
            ->addData('theme', $theme)
            ->getResult();
    }

    #[Post('/config/theme', name: 'Create theme to user', auth: true)]
    #[OA\Post(
        path: '/config/theme',
        summary: 'Create theme to user',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 201, description: 'Theme set'),
            new OA\Response(response: 400, description: 'Missing parameters or invalid parameters'),
        ]
    )]
    private function createThemeToUser(): void
    {
        $themeId = $_POST['theme_id'] ?? "";

        if (empty($themeId)) {
            JsonBuilder::build()
                ->setCode(400)
                ->addData('message', 'Missing parameters')
                ->getResult();
            return;
        }

        if (!is_numeric($themeId)) {
            JsonBuilder::build()
                ->setCode(400)
                ->addData('message', 'Invalid parameters')
                ->getResult();
            return;
        }

        $theme = App::getOrmManager()->connect()->getRepository(UserThemePreferences::class)->createThemePreference($themeId);


        JsonBuilder::build()
            ->setCode(201)
            ->addData('message', 'Theme set')
            ->addData('theme', App::getOrmManager()->connect()->getRepository(Theme::class)->toJson($theme))
            ->getResult();
    }

    #[Put('/config/theme', name: 'Update theme to user', auth: true)]
    #[OA\Put(path: '/config/theme', summary: 'Update theme to user', tags: ['Configuration'])]
    #[OA\Response(response: 201, description: 'Theme set')]
    #[OA\Response(response: 400, description: 'Missing parameters or invalid parameters')]
    private function updateThemeToUser(): void
    {
        global $_REQ;
        RequestUtils::cleanBody();

        $themeId = $_REQ['theme_id'] ?? "";

        if (empty($themeId)) {
            JsonBuilder::build()
                ->setCode(400)
                ->addData('message', 'Missing parameters')
                ->getResult();
            return;
        }

        if (!is_numeric($themeId)) {
            JsonBuilder::build()
                ->setCode(400)
                ->addData('message', 'Invalid parameters')
                ->getResult();
            return;
        }

        $theme = App::getOrmManager()->connect()->getRepository(UserThemePreferences::class)->updateThemePreference($themeId);


        JsonBuilder::build()
            ->setCode(201)
            ->addData('message', 'Theme set')
            ->addData('theme', App::getOrmManager()->connect()->getRepository(Theme::class)->toJson($theme))
            ->getResult();
    }
}