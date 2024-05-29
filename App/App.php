<?php

namespace Descolar;

use Descolar\Adapters as Adapters;
use Descolar\Managers\App\Traits as Traits;
use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\Error\ErrorHandler;
use Descolar\Managers\Router\Router;
use ReflectionException;

/**
 * Main class of Descolar.
 * Should load all the necessary adapters and run the application.
 */
class App
{

    use Traits\ErrorHandlerAdapter;
    use Traits\RouterAdapter;
    use Traits\JsonBuilderAdapter;
    use Traits\EventAdapter;
    use Traits\EnvAdapter;
    use Traits\OrmAdapter;
    use Traits\MailAdapter;
    use Traits\MediaAdapter;
    use Traits\SocketAdapter;

    /**
     * @return bool True if the application is in development mode, false otherwise
     */
    public static function isDev(): bool
    {
        return EnvReader::getInstance()->get('env') === "DEV" ?? false;
    }


    /**
     * Start the application
     *
     * @throws ReflectionException
     */
    public static function run(): void
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        ErrorHandler::handle();
        self::manageRouter();
        self::manageEvent();
        Router::getInstance()->listen();
    }

    /**
     * Set the user uuid
     *
     * @param string $userUuid The user uuid
     * @return void
     */
    public static function setUserUuid(String $userUuid): void
    {
        $_SESSION['userUuid'] = $userUuid;
    }

    /**
     * Get the user uuid
     *
     * @return string|null The user uuid
     */
    public static function getUserUuid(): ?string
    {
        return  $_SESSION['userUuid'] ?? null;
    }

    /**
     * Load all the adapters
     * /!\ It's not a stable method, his content can change at any time /!\
     * @return void
     */
    public static function loadAdapters(): void
    {
        self::useErrorHandler(Adapters\Error\ErrorManager::class);
        self::useEnv(Adapters\Env\EnvManager::class);
        self::useOrm(Adapters\Orm\OrmManager::class);
        self::useRouter(Adapters\Router\RouterRetriever::class);
        self::useEvent(Adapters\Event\EventReader::class);
        self::useJsonBuilder(Adapters\JsonBuilder\JsonBuilderManager::class);
        self::useMail(Adapters\Mail\MailBuilder::class);
        self::useMedia(Adapters\Media\MediaAdapter::class);
        self::useSocket(Adapters\Websocket\MessageManager::class);
    }
}