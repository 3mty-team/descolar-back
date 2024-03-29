<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\App;
use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\User;
use Doctrine\ORM\EntityRepository;

class LoginRepository extends EntityRepository
{
    public function getLoginInformation(String $username, String $password) : ?User
    {
        /**
         * @var User $user
         */
        $user = App::getOrmManager()->connect()->getRepository(User::class)->findOneBy(["username" => $username]);

        /**
         * @var Login $login
         */
        $login = $this->findOneBy(["user" => $user->getId()]);

        $isValid = password_verify($password, $login->getPassword());

        if (!$isValid) {
            return null;
        }
        return $user;
    }
}