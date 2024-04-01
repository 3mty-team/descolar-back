<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Adapters\Router\Utils\RequestUtils;
use Descolar\App;
use Descolar\Data\Entities\Configuration\Session;
use Descolar\Data\Entities\Configuration\Theme;
use Descolar\Data\Entities\Configuration\UserPrivacyPreferences;
use Descolar\Data\Entities\Configuration\UserThemePreferences;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use OpenAPI\Attributes as OA;
use OpenApi\Attributes\RequestBody;

class UserPrivacyPreferencesEndpoint extends AbstractEndpoint
{
    #[Get('/config/privacy', name: 'Retrieve all Themes', auth: true)]
    #[OA\Get(path: "/config/privacy", summary: "Retrieve all Themes", tags: ["Configuration"], responses: [new OA\Response(response: 200, description: "All themes retrieved")])]
    private function getPrivacy(): void
    {
        $userPrivacyPreferences = App::getOrmManager()->connect()->getRepository(UserPrivacyPreferences::class)->getUserPrivacyPreferenceToJson();

        JsonBuilder::build()
            ->setCode(200)
            ->addData('message', 'User privacy preference retrieved')
            ->addData('privacy', $userPrivacyPreferences)
            ->getResult();
    }

    #[Post('/config/privacy', name: 'Create Privacy to user', auth: true)]
    #[OA\Post(
        path: '/config/privacy',
        summary: 'Create Privacy to user',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 201, description: 'Privacy set'),
            new OA\Response(response: 400, description: 'Missing parameters or invalid parameters'),
        ],
    )]
    private function createPrivacyToUser(): void
    {
        $feedVisibility = $_POST['feed_visibility'] ?? "";
        $searchVisibility = $_POST['search_visibility'] ?? "";

        $userPrivacyPreferences = App::getOrmManager()->connect()->getRepository(UserPrivacyPreferences::class)->createUserPrivacyPreference($feedVisibility, $searchVisibility);

        JsonBuilder::build()
            ->setCode(201)
            ->addData('message', 'User privacy preference created')
            ->addData('theme', $userPrivacyPreferences)
            ->getResult();
    }

    #[Put('/config/privacy', name: 'Update Privacy to user', auth: true)]
    #[OA\Put(
        path: '/config/privacy',
        summary: 'Update Privacy to user',
        tags: ['Configuration'],
        responses: [
            new OA\Response(response: 201, description: 'Privacy updated'),
            new OA\Response(response: 400, description: 'Missing parameters or invalid parameters'),
        ],
    )]
    private function updatePrivacyToUser(): void
    {
        global $_REQ;
        RequestUtils::cleanBody();

        $feedVisibility = $_REQ['feed_visibility'] ?? "";
        $searchVisibility = $_REQ['search_visibility'] ?? "";

        $userPrivacyPreferences = App::getOrmManager()->connect()->getRepository(UserPrivacyPreferences::class)->updateUserPrivacyPreference($feedVisibility, $searchVisibility);

        JsonBuilder::build()
            ->setCode(201)
            ->addData('message', 'User privacy preference updated')
            ->addData('theme', $userPrivacyPreferences)
            ->getResult();
    }
}