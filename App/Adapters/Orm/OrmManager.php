<?php
namespace Descolar\Adapters\Orm;

use Descolar\App;
use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\Orm\Exceptions\OrmDataNotDefined;
use Descolar\Managers\Orm\Interfaces\IOrmManager;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\DBAL\Connection;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;
use Psr\Cache\CacheItemPoolInterface;

use Override;

class OrmManager implements IOrmManager
{
    private static EntityManager $_entityManagerInstance;

    /**
     * @return array ORM parameters to connect to the database
     */
    private function getDatabaseParams(): array
    {
        $ormHost = EnvReader::getInstance()->get('ORM_HOST');
        $ormUser = EnvReader::getInstance()->get('ORM_USER');
        $ormPassword = EnvReader::getInstance()->get('ORM_PASSWORD');
        $ormDatabaseName = EnvReader::getInstance()->get('ORM_DATABASE_NAME');
        $ormPort = EnvReader::getInstance()->get('ORM_PORT');
        $ormCharset = EnvReader::getInstance()->get('ORM_CHARSET') ?? 'utf8mb4';

        if(!isset($ormHost, $ormUser, $ormPassword, $ormDatabaseName, $ormPort, $ormCharset)) {
            throw new OrmDataNotDefined();
        }

        return [
            'driver' => "pdo_mysql",
            'host' => $ormHost,
            'user' => $ormUser,
            'password' => $ormPassword,
            'dbname' => $ormDatabaseName,
            'port' => $ormPort,
            'charset' => $ormCharset
        ];
    }

    /**
     * set up the query cache for the ORM
     * @param bool $isDevMode If the application is in development mode
     * @return PhpFilesAdapter|ArrayAdapter The query cache
     */
    private function setQueryCache(bool $isDevMode): PhpFilesAdapter|ArrayAdapter
    {
        return $isDevMode ? new ArrayAdapter() : new PhpFilesAdapter('doctrine_queries');
    }

    /**
     * set up the metadata cache for the ORM
     * @param bool $isDevMode If the application is in development mode
     * @return PhpFilesAdapter|ArrayAdapter The metadata cache
     */
    private function setMetadataCache(bool $isDevMode): PhpFilesAdapter|ArrayAdapter
    {
        return $isDevMode ? new ArrayAdapter() : new PhpFilesAdapter('doctrine_metadata');
    }

    /**
     * Set up the configuration for the ORM
     * @param Configuration $config The configuration to set up
     * @param CacheItemPoolInterface $metadataCache The metadata cache
     * @param MappingDriver $driverImpl The driver to use
     * @param CacheItemPoolInterface $queryCache The query cache
     * @param string $proxyDir The directory to store the proxies
     * @param string $proxyNamespace The namespace for the proxies
     */
    private function setupConfig(Configuration $config, CacheItemPoolInterface $metadataCache, MappingDriver $driverImpl, CacheItemPoolInterface $queryCache, string $proxyDir, string $proxyNamespace): void
    {
        $config->setMetadataCache($metadataCache);
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCache($queryCache);
        $config->setProxyDir($proxyDir);
        $config->setProxyNamespace($proxyNamespace);
        $config->setAutoGenerateProxyClasses(true);
    }

    /**
     * Set up the entity manager
     * @param Connection $connection The connection to the database
     * @param Configuration $config The configuration to use
     */
    private function setEntityManager(Connection $connection, Configuration $config): void
    {
        self::$_entityManagerInstance = new EntityManager($connection, $config);
    }

    #[Override] public function connect(): EntityManager
    {

        if (isset(self::$_entityManagerInstance)) {
            return self::$_entityManagerInstance;
        }

        $paths = ['App/Entities'];
        $proxyDir = DIR_ROOT . '\App\Adapters\Orm\Proxies';
        $proxyNamespace = 'Descolar\Adapters\Orm\Proxies';

        $isDevMode = App::isDev();
        $dbParams = $this->getDatabaseParams();
        $queryCache = $this->setQueryCache($isDevMode);
        $metadataCache = $this->setMetadataCache($isDevMode);

        $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode, $proxyDir);
        $driverImpl = new AttributeDriver($paths, true);

        $this->setupConfig($config, $metadataCache, $driverImpl, $queryCache, $proxyDir, $proxyNamespace);

        $connection = DriverManager::getConnection($dbParams, $config);

        $this->setEntityManager($connection, $config);

        return self::$_entityManagerInstance;
    }
}