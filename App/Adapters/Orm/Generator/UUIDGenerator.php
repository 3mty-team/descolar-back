<?php

namespace Descolar\Adapters\Orm\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Random\RandomException;

class UUIDGenerator extends AbstractIdGenerator
{

    /**
     * @throws RandomException
     */
    #[\Override] public function generateId(EntityManagerInterface $em, ?object $entity): string
    {
        $data = random_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}