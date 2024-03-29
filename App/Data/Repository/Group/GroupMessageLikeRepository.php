<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Data\Entities\Group\GroupMessageLike;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Doctrine\ORM\EntityRepository;

class GroupMessageLikeRepository extends EntityRepository
{

    public function getUsersLikeByMessageId(GroupMessage $groupMessage): array
    {
        return $this->findBy(['groupMessage' => $groupMessage, 'isActive' => 1]);
    }

    public function getGroupMessageLike(GroupMessage $groupMessage, User $user): ?GroupMessageLike
    {

        $groupMessageLike = $this->findOneBy(['groupMessage' => $groupMessage, 'user' => $user]);

        if ($groupMessageLike === null) {
            return null;
        }

        return $groupMessageLike;
    }

    public function like(int $groupId, int $messageId): GroupMessageLike
    {
        $groupMessage = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->findById($groupId, $messageId);

        $user = App::getOrmManager()->connect()->getRepository(User::class)->find(App::getUserUuid());

        if ($user === null) {
            throw new EndpointException('User not logged', 403);
        }

        $groupMessageLike = $this->getGroupMessageLike($groupMessage, $user);
        if ($groupMessageLike !== null && !$groupMessageLike->isActive()) {
            $groupMessageLike->setIsActive(true);
            App::getOrmManager()->connect()->persist($groupMessageLike);
            App::getOrmManager()->connect()->flush();
            return $groupMessageLike;
        }

        $groupMessageLike = new GroupMessageLike();
        $groupMessageLike->setGroupMessage($groupMessage);
        $groupMessageLike->setUser($user);
        $groupMessageLike->setLikeDate(new DateTime());
        $groupMessageLike->setIsActive(true);

        App::getOrmManager()->connect()->persist($groupMessageLike);
        App::getOrmManager()->connect()->flush();

        return $groupMessageLike;
    }

    public function unlike(int $groupId, int $messageId): int
    {
        $groupMessage = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->findById($groupId, $messageId);

        $user = App::getOrmManager()->connect()->getRepository(User::class)->find(App::getUserUuid());

        if ($user === null) {
            throw new EndpointException('User not logged', 403);
        }

        $groupMessageLike = $this->getGroupMessageLike($groupMessage, $user);

        if ($groupMessageLike === null || !$groupMessageLike->isActive()) {
            throw new EndpointException('User not liked this message', 400);
        }

        $groupMessageLike->setIsActive(false);
        App::getOrmManager()->connect()->persist($groupMessageLike);
        App::getOrmManager()->connect()->flush();

        return $groupMessageLike->getGroupMessage()->getId();
    }

    public function toJson(int $groupId, int $messageId)
    {

        $groupMessage = App::getOrmManager()->connect()->getRepository(GroupMessage::class)->findById($groupId, $messageId);
        $groupLikes = $this->getUsersLikeByMessageId($groupMessage);

        $users = [];
        foreach ($groupLikes as $groupLike) {
            /** @var GroupMessageLike $groupMessage */
            $users[] = App::getOrmManager()->connect()->getRepository(User::class)->toReduceJson($groupLike->getUser());
        }

        return [
            "group" => App::getOrmManager()->connect()->getRepository(Group::class)->toJson($groupMessage->getGroupMessage()->getGroup()),
            "likes" => $users,
        ];

    }

}