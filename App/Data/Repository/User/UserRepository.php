<?php

namespace Descolar\Data\Repository\User;

use Descolar\Data\Entities\Group\Group;
use Descolar\Data\Entities\User\User;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function toReduceJson(User $user): array {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'isActive' => $user->isActive(),
        ];
    }

    public function toJson(User $user): array {
        return [
            'uuid' => $user->getUUID(),
            'username' => $user->getUsername(),
            'pfpPath' => $user->getProfilePicturePath(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'mail' => $user->getMail(),
            'date' => $user->getDate()?->format('d-m-Y'),
            'biography' => $user->getBiography(),
            'isActive' => $user->isActive(),
        ];
    }
}