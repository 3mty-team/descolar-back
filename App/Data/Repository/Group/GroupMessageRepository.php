<?php

namespace Descolar\Data\Repository\Group;

use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

class GroupMessageRepository extends EntityRepository
{

    public function findById(int $groupId, int $messageId, bool $checkActive = true): GroupMessage
    {
        if ($checkActive) {
            $groupMessage = $this->findOneBy(['group' => $groupId, 'id' => $messageId, 'isActive' => 1]);
        } else {
            $groupMessage = $this->findOneBy(['group' => $groupId, 'id' => $messageId]);
        }


        if ($groupMessage === null) {
            throw new EndpointException('Message not found', 404);
        }

        return $groupMessage;
    }

    /**
     * @return array<GroupMessage> List of messages
     */
    public function findAllInRange(int $groupId, int $range, ?int $timestamp): array
    {
        if ($range < 1) {
            throw new EndpointException('Range must be greater than 0', 400);
        }

        $group = OrmConnector::getInstance()->getRepository(Group::class)->findById($groupId);

        $qb = $this->createQueryBuilder('gm')
            ->select('gm')
            ->where('gm.group = :groupId')
            ->andWhere('gm.isActive = 1')
            ->setParameter('groupId', $group->getId())
            ->orderBy('gm.date', 'DESC')
            ->setMaxResults($range);

        if ($timestamp) {
            $date = new \DateTime("@$timestamp");
            $qb->andWhere('gm.date > :timestamp')
                ->setParameter('timestamp', $date);
        }

        return $qb->getQuery()->getResult();
    }

    public function create(int $groupId, ?string $content, ?int $date, ?array $medias): GroupMessage
    {

        $medias ??= [];
        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();
        $group = OrmConnector::getInstance()->getRepository(Group::class)->findById($groupId);

        $groupMessage = new GroupMessage();
        $groupMessage->setGroup($group);
        $groupMessage->setUser(OrmConnector::getInstance()->getRepository(User::class)->find(App::getUserUuid()));
        $groupMessage->setContent($content);
        $groupMessage->setDate(new \DateTime("@$date"));
        $groupMessage->setIsActive(true);
        foreach ($medias as $media) {
            $mediaObj = OrmConnector::getInstance()->getRepository(Media::class)->findById($media);
            $groupMessage->addMedia($mediaObj);
        }

        Validator::getInstance($groupMessage)->check();

        OrmConnector::getInstance()->persist($groupMessage);
        OrmConnector::getInstance()->flush();

        return $groupMessage;
    }

    public function update(int $groupId, ?int $messageId, ?string $content, ?array $medias): GroupMessage
    {

        $medias ??= [];
        $groupMessage = $this->findById($groupId, $messageId);

        if (!empty($content)) {
            $groupMessage->setContent($content);
        }

        foreach ($medias as $media) {
            $mediaObj = OrmConnector::getInstance()->getRepository(Media::class)->findById($media);
            $groupMessage->addMedia($mediaObj);
        }

        Validator::getInstance($groupMessage)->check();

        OrmConnector::getInstance()->persist($groupMessage);
        OrmConnector::getInstance()->flush();

        return $groupMessage;
    }

    public function delete(int $groupId, int $messageId): int
    {
        $groupMessage = $this->findById($groupId, $messageId);

        $groupMessage->setIsActive(false);

        Validator::getInstance($groupMessage)->check();

        OrmConnector::getInstance()->persist($groupMessage);
        OrmConnector::getInstance()->flush();

        return $groupMessage->getId();
    }

    public function deleteByMessageId(int $messageId): int
    {
        $groupMessage = $this->find($messageId);

        if ($groupMessage === null) {
            throw new EndpointException('Message not found', 404);
        }

        $groupMessage->setIsActive(false);

        OrmConnector::getInstance()->persist($groupMessage);
        OrmConnector::getInstance()->flush();

        return $groupMessage->getId();
    }

    public function toJsonRange(int $groupId, int $range = 10, int $timestamp = null): array
    {
        $groupMessages = $this->findAllInRange($groupId, $range, $timestamp);

        $group = OrmConnector::getInstance()->getRepository(Group::class)->findById($groupId);

        $messages = [];
        foreach ($groupMessages as $groupMessage) {
            $messages[] = [
                'id' => $groupMessage->getId(),
                'user' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($groupMessage->getUser()),
                'content' => $groupMessage->getContent(),
                'date' => $groupMessage->getDate()->format('d-m-Y H:i:s'),
                'isActive' => $groupMessage->isActive(),
                'medias' => $groupMessage->getMedias()->map(fn($media) => $media->getId())->toArray()
            ];
        }

        return [
            'group' => OrmConnector::getInstance()->getRepository(Group::class)->toJson($group),
            'messages' => $messages
        ];
    }

    public function toJson(GroupMessage $groupMessage): array
    {
        $groupMessage = $this->findById($groupMessage->getGroup()->getId(), $groupMessage->getId(), false);

        return [
            'id' => $groupMessage->getId(),
            'group' => OrmConnector::getInstance()->getRepository(Group::class)->toJson($groupMessage->getGroup()),
            'user' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($groupMessage->getUser()),
            'content' => $groupMessage->getContent(),
            'date' => $groupMessage->getDate()->format('d-m-Y H:i:s'),
            'isActive' => $groupMessage->isActive(),
            'medias' => $groupMessage->getMedias()->map(fn($media) => $media->getId())->toArray()
        ];
    }

}