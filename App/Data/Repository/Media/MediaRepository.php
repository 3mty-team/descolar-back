<?php

namespace Descolar\Data\Repository\Media;

use Descolar\Adapters\Media\Types\Image;
use Descolar\Adapters\Media\Types\Video;
use Descolar\Data\Entities\Media\Media;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Media\Interfaces\IMedia;
use Descolar\Managers\Media\MediaManager;
use Doctrine\ORM\EntityRepository;

class MediaRepository extends EntityRepository
{

    public function getTypes(): array
    {
        return [
            (new Image())->__toString(),
            (new Video())->__toString()
        ];
    }

    public function findById(int $id): Media
    {
        $media = $this->createQueryBuilder('m')
            ->where('m.id = :id')
            ->andWhere('m.isActive = true')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        if ($media === null) {
            throw new EndpointException("Media not found", 404);
        }

        return $media;
    }

    public function findByUrl(string $url): Media
    {
        $media = $this->createQueryBuilder('m')
            ->where('m.path = :url')
            ->andWhere('m.isActive = true')
            ->setParameter('url', $url)
            ->getQuery()
            ->getOneOrNullResult();

        if ($media === null) {
            throw new EndpointException("Media not found", 404);
        }

        return $media;
    }

    private function createMedia(IMedia $media): Media
    {
        $mediaEntity = new Media();
        $mediaEntity->setPath($media->getUrl());
        $mediaEntity->setMediaType($media->getType()->toMediaType());
        $mediaEntity->setIsActive(true);

        $this->getEntityManager()->persist($mediaEntity);
        $this->getEntityManager()->flush();

        return $mediaEntity;
    }

    /**
     * @param IMedia[] $mediaList
     * @return Media[]
     */
    public function create(array $mediaList): array
    {
        $medias = [];

        foreach ($mediaList as $media) {
            $medias[] = $this->createMedia($media);
        }

        return $medias;
    }

    public function delete(int $id): int
    {
        $media = $this->findById($id);

        if (!$media->isActive()) {
            throw new EndpointException("Media already deleted", 403);
        }

        $media->setIsActive(false);

        $this->getEntityManager()->flush();

        $mediaObject = MediaManager::getInstance()->generateMedia($media);

        MediaManager::getInstance()->disableMedia($mediaObject);

        return $id;
    }

    public function toJson(Media $media): array
    {
        return [
            'id' => $media->getId(),
            'path' => $media->getPath(),
            'type' => $media->getMediaType(),
            'isActive' => $media->isActive()
        ];
    }

}