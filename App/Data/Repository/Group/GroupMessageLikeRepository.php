<?php

namespace Descolar\Data\Repository\Group;

use DateTime;
use Descolar\App;
use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\Group\GroupMessage;
use Descolar\Data\Entities\Group\GroupMessageLike;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
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
        $groupMessage = OrmConnector::getInstance()->getRepository(GroupMessage::class)->findById($groupId, $messageId);

        $user = OrmConnector::getInstance()->getRepository(User::class)->findByUUID(App::getUserUuid());

        $groupMessageLike = $this->getGroupMessageLike($groupMessage, $user);
        if ($groupMessageLike !== null) {

            if($groupMessageLike->isActive() === true){
                throw new EndpointException('User already liked this message', 400);
            }

            $groupMessageLike->setIsActive(true);

            Validator::getInstance($groupMessageLike)->check();

            OrmConnector::getInstance()->persist($groupMessageLike);
            OrmConnector::getInstance()->flush();
            return $groupMessageLike;
        }

        $groupMessageLike = new GroupMessageLike();
        $groupMessageLike->setGroupMessage($groupMessage);
        $groupMessageLike->setUser($user);
        $groupMessageLike->setLikeDate(new DateTime());
        $groupMessageLike->setIsActive(true);

        Validator::getInstance($groupMessageLike)->check();

        OrmConnector::getInstance()->persist($groupMessageLike);
        OrmConnector::getInstance()->flush();

        return $groupMessageLike;
    }

    public function unlike(int $groupId, int $messageId): int
    {
        $groupMessage = OrmConnector::getInstance()->getRepository(GroupMessage::class)->findById($groupId, $messageId);

        $user = OrmConnector::getInstance()->getRepository(User::class)->findByUUID(App::getUserUuid());

        $groupMessageLike = $this->getGroupMessageLike($groupMessage, $user);

        if ($groupMessageLike === null || !$groupMessageLike->isActive()) {
            throw new EndpointException('User not liked this message', 400);
        }

        $groupMessageLike->setIsActive(false);

        Validator::getInstance($groupMessageLike)->check();

        OrmConnector::getInstance()->persist($groupMessageLike);
        OrmConnector::getInstance()->flush();

        return $groupMessageLike->getGroupMessage()->getId();
    }

    public function toJson(int $groupId, int $messageId)
    {


        /** @var GroupMessage $groupMessage */
        $groupMessage = OrmConnector::getInstance()->getRepository(GroupMessage::class)->findById($groupId, $messageId);
        $groupLikes = $this->getUsersLikeByMessageId($groupMessage);

        $users = [];
        foreach ($groupLikes as $groupLike) {
            /** @var GroupMessageLike $groupLike */
            $users[] = OrmConnector::getInstance()->getRepository(User::class)->toReduceJson($groupLike->getUser());
        }

        return [
            "group" => OrmConnector::getInstance()->getRepository(Group::class)->toJson($groupMessage->getGroup()),
            "likes" => $users,
        ];

    }

}