<?php

namespace Descolar\Adapters\Orm;

use Descolar\App;
use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\Orm\Exceptions\OrmDataNotDefined;
use Descolar\Managers\Orm\Interfaces\IOrmManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\ORMSetup;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Override;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

class OrmManager implements IOrmManager
{
    private static EntityManager $_entityManagerInstance;
    private PhpFilesAdapter|ArrayAdapter $_queryCache;
    private PhpFilesAdapter|ArrayAdapter $_metadataCache;

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

        if (!isset($ormHost, $ormUser, $ormPassword, $ormDatabaseName, $ormPort, $ormCharset)) {
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
        $this->_queryCache = $isDevMode ? new ArrayAdapter() : new PhpFilesAdapter('doctrine_query_cache');
        return $this->_queryCache;
    }

    /**
     * get the query cache
     * @return PhpFilesAdapter|ArrayAdapter The query cache
     */
    private function getQueryCache(): PhpFilesAdapter|ArrayAdapter
    {
        return $this->_queryCache;
    }

    /**
     * set up the metadata cache for the ORM
     * @param bool $isDevMode If the application is in development mode
     * @return PhpFilesAdapter|ArrayAdapter The metadata cache
     */
    private function setMetadataCache(bool $isDevMode): PhpFilesAdapter|ArrayAdapter
    {
        $this->_metadataCache = $isDevMode ? new ArrayAdapter() : new PhpFilesAdapter('doctrine_metadata_cache');
        return $this->_metadataCache;
    }

    /**
     * get the metadata cache
     * @return PhpFilesAdapter|ArrayAdapter The metadata cache
     */
    private function getMetadataCache(): PhpFilesAdapter|ArrayAdapter
    {
        return $this->_metadataCache;
    }

    /**
     * Refresh the cache
     */
    private function refreshCache(): void
    {
        $this->getQueryCache()->clear();
        $this->getMetadataCache()->clear();
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
        $this->refreshCache();

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

        $directorySeparator = DIRECTORY_SEPARATOR;

        $paths = ["App{$directorySeparator}Entities"];
        $proxyDir = DIR_ROOT . "{$directorySeparator}App{$directorySeparator}Adapters{$directorySeparator}Orm{$directorySeparator}Proxies";
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