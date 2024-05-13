<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Doctrine\ORM\EntityRepository;

class LoginRepository extends EntityRepository
{

    private function getUserByUsernameOrEmail(String $username): ?User
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(["username" => $username]);
        if($user == null) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(["email" => $username]);
        }

        if(!$user) {
            return null;
        }

        return OrmConnector::getInstance()->getRepository(User::class)->findByUUID($user->getUUID());
    }

    public function createLogin(User $user, String $password): ?Login
    {
        $login = new Login();
        $login->setUser($user);
        $login->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $this->getEntityManager()->persist($login);
        $this->getEntityManager()->flush();
        return $login;
    }

    public function getLoginInformation(String $username, String $password) : User
    {
        /**
         * @var User $user
         * @var Login $login
         */
        $user = $this->getUserByUsernameOrEmail($username);

        if($user->getToken() != null) {
            throw new EndpointException("Email not verified", 403);
        }

        $isValid = password_verify($password, $login?->getPassword());
        if (!$isValid) {
            throw new EndpointException("Invalid login or password", 403);
        }

        return $user;
    }
}