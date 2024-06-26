<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\Configuration\Login;
use Descolar\Data\Entities\User\User;
use Descolar\Managers\Endpoint\Exceptions\EndpointException;
use Descolar\Managers\Orm\OrmConnector;
use Descolar\Managers\Validator\Validator;
use Doctrine\ORM\EntityRepository;

class LoginRepository extends EntityRepository
{

    private function getUserByUsernameOrEmail(string $usernameOrMail): ?User
    {
        $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(["username" => $usernameOrMail]);
        if($user == null) {
            $user = OrmConnector::getInstance()->getRepository(User::class)->findOneBy(["mail" => $usernameOrMail]);
        }

        if(!$user) {
            return null;
        }

        return $user;
    }

    public function createLogin(User $user, string $password): ?Login
    {
        $login = new Login();
        $login->setUser($user);
        $login->setPassword(password_hash($password, PASSWORD_DEFAULT));

        Validator::getInstance($login)->check();

        $this->getEntityManager()->persist($login);
        $this->getEntityManager()->flush();
        return $login;
    }

    public function getLoginInformation(?string $username, ?string $password) : User
    {
        /**
         * @var User $user
         * @var Login $login
         */
        $user = $this->getUserByUsernameOrEmail($username);
        $login = $this->findOneBy(["user" => $user?->getUUID()]);

        if($user == null || $login == null) {
            throw new EndpointException("Identifiant ou mot de passe invalide", 403);
        }

        if($user->getToken() != null) {
            throw new EndpointException("Veuillez confirmez votre email", 403);
        }

        $isValid = password_verify($password, $login->getPassword());
        if (!$isValid) {
            throw new EndpointException("Identifiant ou mot de passe invalide", 403);
        }

        return $user;
    }
}