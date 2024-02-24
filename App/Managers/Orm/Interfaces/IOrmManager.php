<?php

namespace Descolar\Managers\Orm\Interfaces;

use Doctrine\ORM\EntityManagerInterface;

interface IOrmManager
{
    /**
     * Perform the connection to the database and return the EntityManager a tool to use an ORM.
     *
     * @return EntityManagerInterface An instance of the EntityManager.
     */
    public function connect(): EntityManagerInterface;
}