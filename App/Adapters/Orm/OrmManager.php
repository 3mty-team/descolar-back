<?php
namespace Descolar\Adapters\Orm;

use Override;
use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\Orm\Exceptions\OrmDataNotDefined;
use Descolar\Managers\Orm\Interfaces\IOrmManager;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class OrmManager implements IOrmManager
{
    private static EntityManager $_entityManagerInstance;
    #[Override] public function connect(): EntityManager
    {

        if (isset(self::$_entityManagerInstance)) {
            return self::$_entityManagerInstance;
        }

        $paths = ['App/Entities'];
        $isDevMode = EnvReader::getInstance()->get('is_dev') === 'dev' ?? false;

        $ormHost = EnvReader::getInstance()->get('ORM_HOST');
        $ormUser = EnvReader::getInstance()->get('ORM_USER');
        $ormPassword = EnvReader::getInstance()->get('ORM_PASSWORD');
        $ormDatabaseName = EnvReader::getInstance()->get('ORM_DATABASE_NAME');
        $ormPort = EnvReader::getInstance()->get('ORM_PORT');
        $ormCharset = EnvReader::getInstance()->get('ORM_CHARSET') ?? 'utf8mb4';

        if(!isset($ormHost, $ormUser, $ormPassword, $ormDatabaseName, $ormPort, $ormCharset)) {
            throw new OrmDataNotDefined();
        }

        $dbParams = [
            'driver' => "pdo_mysql",
            'host' => $ormHost,
            'user' => $ormUser,
            'password' => $ormPassword,
            'dbname' => $ormDatabaseName,
            'port' => $ormPort,
            'charset' => $ormCharset
        ];

        $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);
        $connection = DriverManager::getConnection($dbParams, $config);

        self::$_entityManagerInstance = new EntityManager($connection, $config);

        return self::$_entityManagerInstance;

    }
}