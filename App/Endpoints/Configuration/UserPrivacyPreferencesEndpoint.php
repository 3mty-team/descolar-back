<?php

namespace Descolar\Endpoints\Configuration;

use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\Annotations\Put;
use Descolar\Data\Entities\Configuration\UserPrivacyPreferences;
use Descolar\Managers\Endpoint\AbstractEndpoint;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Requester\Requester;
use OpenAPI\Attributes as OA;

class UserPrivacyPreferencesEndpoint extends AbstractEndpoint
{
    #[Get('/config/privacy', name: 'Retrieve all Themes', auth: true)]
    #[OA\Get(path: "/config/privacy", summary: "Retrieve all Themes", tags: ["Configuration"], responses: [new OA\Response(response: 200, description: "All themes retrieved")])]
    private function getPrivacy(): void
    {
        $this->reply(function ($response) {
            $userPrivacyPreferences = OrmConnector::getInstance()->getRepository(UserPrivacyPreferences::class)->getUserPrivacyPreferenceToJson();

            foreach ($userPrivacyPreferences as $key => $value) {
                $response->addData($key, $value);
            }
        });
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
        $this->reply(function ($response) {
            [$feedVisibility, $searchVisibility] = Requester::getInstance()->trackMany(
                "feed_visibility", "search_visibility"
            );

            $userPrivacyPreferences = OrmConnector::getInstance()->getRepository(UserPrivacyPreferences::class)->createUserPrivacyPreference($feedVisibility, $searchVisibility);

            foreach ($userPrivacyPreferences as $key => $value) {
                $response->addData($key, $value);
            }
        });
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
        $this->reply(function ($response) {
            [$feedVisibility, $searchVisibility] = Requester::getInstance()->trackMany(
                "feed_visibility", "search_visibility"
            );

            $userPrivacyPreferences = OrmConnector::getInstance()->getRepository(UserPrivacyPreferences::class)->updateUserPrivacyPreference($feedVisibility, $searchVisibility);

            foreach ($userPrivacyPreferences as $key => $value) {
                $response->addData($key, $value);
            }
        });
    }
}