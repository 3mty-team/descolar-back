<?php

namespace Descolar\Managers\Orm;

use Descolar\App;
use Doctrine\ORM\EntityManagerInterface;

class OrmConnector
{

    /**
     * Get the instance of the ORM manager
     * @return EntityManagerInterface The instance of the ORM manager
     */
    public static function getInstance(): EntityManagerInterface
    {
        return App::getOrmManager()->connect();
    }

}