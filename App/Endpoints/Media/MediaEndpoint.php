<?php

namespace Descolar\Endpoints\Media;

use Descolar\Adapters\Router\Annotations\Delete;
use Descolar\Adapters\Router\Annotations\Get;
use Descolar\Adapters\Router\Annotations\Post;
use Descolar\Adapters\Router\RouteParam;
use Descolar\Data\Entities\Media\Media;
use Descolar\Managers\Endpoint\AbstractEndpoint;

use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\JsonBuilder\JsonBuilder;
use Descolar\Managers\Media\MediaManager;
use Descolar\Managers\Orm\OrmConnector;
use OpenAPI\Attributes as OA;


class MediaEndpoint extends AbstractEndpoint
{

    #[Get('/media/types', name: 'getAllMediaTypes', auth: true)]
    #[OA\Get(path: "/media/types", summary: "getAllMediaTypes", tags: ["Media"], responses: [new OA\Response(response: 200, description: "All media types retrieved")])]
    private function getAllMediaTypes(): void
    {

        $response = JsonBuilder::build();
        $typeList = OrmConnector::getInstance()->getRepository(Media::class)->getTypes();

        $response->addData('medias', $typeList);
        $response->setCode(200);
        $response->getResult();
    }

    #[Get('/media/:id', variables: ["id" => RouteParam::NUMBER], name: 'getMediaById', auth: true)]
    #[OA\Get(path: "/media/{id}", summary: "getMediaById", tags: ["Media"], parameters: [new OA\PathParameter("id", "id", "Media ID", required: true)], responses: [new OA\Response(response: 200, description: "Media retrieved")])]
    private function getMediaById(int $mediaId): void
    {

        $response = JsonBuilder::build();

        try {
            $media = OrmConnector::getInstance()->getRepository(Media::class)->findById($mediaId);
            $mediaData = OrmConnector::getInstance()->getRepository(Media::class)->toJson($media);

            foreach ($mediaData as $key => $value) {
                $response->addData($key, $value);
            }

            $response->setCode(200);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }

    }

    #[Post('/media', name: 'createMedia', auth: true)]
    #[OA\Post(path: "/media", summary: "createMedia", tags: ["Media"], responses: [new OA\Response(response: 201, description: "Media created")])]
    private function createMedia(): void
    {
        $response = JsonBuilder::build();

        try {
            $mediasToSave = MediaManager::getInstance()->saveMediaList();
            $medias = OrmConnector::getInstance()->getRepository(Media::class)->create($mediasToSave);

            $mediasToJson = array_map(fn($media) => OrmConnector::getInstance()->getRepository(Media::class)->toJson($media), $medias);

            $response->setCode(200);
            $response->addData('medias', $mediasToJson);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

    #[Delete('/media/:id', variables: ["id" => RouteParam::NUMBER], name: 'deleteMedia', auth: true)]
    #[OA\Delete(path: "/media/{id}", summary: "deleteMedia", tags: ["Media"], parameters: [new OA\PathParameter("id", "id", "Media ID", required: true)], responses: [new OA\Response(response: 204, description: "Media deleted")])]
    private function deleteMedia(int $mediaId): void
    {
        $response = JsonBuilder::build();

        try {
            $mediaId = OrmConnector::getInstance()->getRepository(Media::class)->delete($mediaId);

            $response->setCode(200);
            $response->addData('id', $mediaId);
            $response->getResult();
        } catch (EndpointException $e) {
            $response->setCode($e->getCode());
            $response->addData('message', $e->getMessage());
            $response->getResult();
        }
    }

}