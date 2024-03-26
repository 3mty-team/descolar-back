<?php

namespace Descolar\Data\Repository\Configuration;

use Descolar\Data\Entities\User\User;
use Doctrine\ORM\EntityRepository;

class LoginRepository extends EntityRepository
{
    public function getLoginInformation(String $username, String $password) : User
    {
        return $this->findOneBy(['username' => $username, 'password' => $password]);
    }
}