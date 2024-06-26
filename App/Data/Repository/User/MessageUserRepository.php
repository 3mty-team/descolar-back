<?php

namespace Descolar\Data\Repository\User;

use DateTime;
use DateTimeZone;
use Descolar\App;
use Descolar\Data\Entities\Media\Media;
use Descolar\Data\Entities\User\MessageUser;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class MessageUserRepository extends EntityRepository
{

    private function queryMessages(User $firstUser, User $secondUser, int $range, ?int $timestamp): array
    {
        $qb = $this->createQueryBuilder('mu')
            ->select('mu')
            ->where('mu.sender = :firstUser AND mu.receiver = :secondUser')
            ->orWhere('mu.sender = :secondUser AND mu.receiver = :firstUser')
            ->setParameter('firstUser', $firstUser)
            ->setParameter('secondUser', $secondUser)
            ->orderBy('mu.date', 'DESC')
            ->setMaxResults($range);

        if ($timestamp) {
            $date = new DateTime("@$timestamp", new DateTimeZone('Europe/Paris'));
            $qb->andWhere('mu.date > :timestamp')
                ->setParameter('timestamp', $date);
        }

        return $qb->getQuery()->getResult();
    }

    private function findAllInRange(string $userUUID, int $range, ?int $timestamp): array
    {
        if ($range < 1) {
            throw new EndpointException('Range must be greater than 0', 400);
        }

        $receiver = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($userUUID);
        $sender = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        return $this->queryMessages($sender, $receiver, $range, $timestamp);
    }

    public function findById(int $id): MessageUser|int
    {
        $message = $this->find($id);

        if ($message === null) {
            throw new EndpointException("Message not found", 404);
        }

        return $message;
    }

    private function manageLikes(int $messageId, bool $needToLike): MessageUser
    {
        $message = $this->find($messageId);
        if($message === null) {
            throw new EndpointException('Message not found', 404);
        }

        $user = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        switch ($user) {
            case $message->getSender():
                $message->setIsLikedBySender($needToLike);
                break;
            case $message->getReceiver():
                $message->setIsLikedByReceiver($needToLike);
                break;
            default:
                throw new EndpointException('User not allowed to like this message', 403);
        }

        OrmConnector::getInstance()->persist($message);
        OrmConnector::getInstance()->flush();

        return $message;
    }

    public function create(?string $receiverUUID, ?string $content, ?int $date, ?array $medias): MessageUser
    {

        if(empty($content) || empty($receiverUUID)) {
            throw new EndpointException('Missing parameters "Content", "receiverUUID"', 400);
        }

        $medias ??= [];

        $receiver = OrmConnector::getInstance()->getRepository(User::class)->findByUuid($receiverUUID);
        $sender = OrmConnector::getInstance()->getRepository(User::class)->getLoggedUser();

        $message = new MessageUser();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setIsLikedBySender(false);
        $message->setIsLikedByReceiver(false);
        $message->setContent($content);
        $message->setDate(new DateTime("@$date", new DateTimeZone('Europe/Paris')));
        $message->setIsActive(true);

        foreach ($medias as $media) {
            $media = OrmConnector::getInstance()->getRepository(Media::class)->findById($media);
            $message->addMedia($media);
        }

        OrmConnector::getInstance()->persist($message);
        OrmConnector::getInstance()->flush();

        return $message;
    }

    public function like(int $messageId): MessageUser
    {
        return $this->manageLikes($messageId, true);
    }

    public function unlike(int $messageId): MessageUser
    {
        return $this->manageLikes($messageId, false);
    }

    public function delete(int $messageId): int
    {
        $message = $this->find($messageId);
        if($message === null) {
            throw new EndpointException('Message not found', 404);
        }

        $message->setIsActive(false);

        OrmConnector::getInstance()->flush();

        return $messageId;
    }

    public function toJsonRange(int $range, string $userUUID, ?int $timestamp): array
    {

        $messages = $this->findAllInRange($userUUID, $range, $timestamp);

        $messageUsers = [];
        foreach ($messages as $message) {

            /** @var MessageUser $message */
            $sender = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $message->getSender()->getUUID()]);
            $receiver = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(['uuid' => $message->getReceiver()->getUUID()]);

            $messageUsers[] = [
                'id' => $message->getId(),
                'sender' => $sender ? OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($sender) : null,
                'receiver' => $receiver ? OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($receiver) : null,
                'content' => $message->getContent(),
                'isLikedBySender' => $message->isLikedBySender(),
                'isLikedByReceiver' => $message->isLikedByReceiver(),
                'date' => $message->getDate(),
                'isActive' => $message->isActive(),
                'medias' => $message->getMedias()->map(fn($media) => $media->getId())->toArray(),
            ];
        }

        return $messageUsers;
    }

    public function toJson(MessageUser $message) {

        return [
            'id' => $message->getId(),
            'sender' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($message->getSender()),
            'receiver' => OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($message->getReceiver()),
            'content' => $message->getContent(),
            'isLikedBySender' => $message->isLikedBySender(),
            'isLikedByReceiver' => $message->isLikedByReceiver(),
            'date' => $message->getDate(),
            'medias' => $message->getMedias()->map(fn($media) => $media->getId())->toArray(),
            'isActive' => $message->isActive(),
        ];
    }

}