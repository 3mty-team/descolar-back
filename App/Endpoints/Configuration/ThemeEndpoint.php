<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\Data\Entities\Configuration\Theme;
use Descolar\Data\Entities\Configuration\UserThemePreferences;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;

class ThemeEndpoint extends AbstractEndpoint
{
    #[Get('/config/themes', name: 'Retrieve all Themes', auth: true)]
    #[OA\Get(path: "/config/themes", summary: "Retrieve all Themes", tags: ["Configuration"], responses: [new OA\Response(response: 200, description: "All themes retrieved")])]
    private function getAllThemes(): void
    {
        $this->reply(function ($response) {
            $themes = OrmConnector::getInstance()->getRepository(Theme::class)->getAllThemesToJson();

            $response->addData('message', 'All themes retrieved');
            $response->addData('themes', $themes);
        });
    }

    #[Get('/config/theme', name: 'Retrieve Theme preference', auth: true)]
    #[OA\Get(path: '/config/theme', summary: 'Retrieve Theme preference', tags: ['Configuration'], responses: [new OA\Response(response: 200, description: 'Theme preference retrieved')])]
    private function getThemePreference(): void
    {
        $this->reply(function ($response) {
            $theme = OrmConnector::getInstance()->getRepository(UserThemePreferences::class)->getThemePreferenceToJson();

            $response->addData('message', 'Theme preference retrieved');
            $response->addData('theme', $theme);
        });
    }

    #[Post('/config/theme', name: 'Create theme to user', auth: true)]
    #[OA\Post(
        path: '/config/theme',
        summary: 'Create theme to user',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 201, description: 'Theme set'),
            new OA\Response(response: 400, description: 'Missing parameters or invalid parameters'),
        ],
    )]
    private function createThemeToUser(): void
    {
        $this->reply(function ($response) {
            $themeId = $_POST['theme_id'] ?? "";

            if (empty($themeId)) {
                $response->addData('message', 'Missing parameters');
                return;
            }

            if (!is_numeric($themeId)) {
                $response->addData('message', 'Invalid parameters');
                return;
            }

            $theme = OrmConnector::getInstance()->getRepository(UserThemePreferences::class)->createThemePreference($themeId);

            $response->addData('message', 'Theme set')
                ->addData('theme', OrmConnector::getInstance()->getRepository(Theme::class)->toJson($theme));
        });
    }

    #[Put('/config/theme', name: 'Update theme to user', auth: true)]
    #[OA\Put(path: '/config/theme', summary: 'Update theme to user', tags: ['Configuration'], responses: [new OA\Response(response: 201, description: 'Theme set'), new OA\Response(response: 400, description: 'Missing parameters or invalid parameters')])]
    private function updateThemeToUser(): void
    {
        $this->reply(function ($response) {
            global $_REQ;
            RequestUtils::cleanBody();

            $themeId = $_REQ['theme_id'] ?? "";

            if (empty($themeId)) {
                $response->addData('message', 'Missing parameters');
                return;
            }

            if (!is_numeric($themeId)) {
                $response->addData('message', 'Invalid parameters');
                return;
            }

            $theme = OrmConnector::getInstance()->getRepository(UserThemePreferences::class)->updateThemePreference($themeId);

            $response->addData('message', 'Theme set')
                ->addData('theme', OrmConnector::getInstance()->getRepository(Theme::class)->toJson($theme));
        });
    }
}