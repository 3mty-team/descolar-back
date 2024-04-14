<?php

namespace Descolar;

use Descolar\Managers\Env\EnvReader;
use Descolar\Managers\Error\ErrorHandler;
use Descolar\Managers\Router\Router;
use Descolar\Managers\App\Traits\{EnvAdapter,
    ErrorHandlerAdapter,
    JsonBuilderAdapter,
    MailAdapter,
    OrmAdapter,
    RouterAdapter,
    EventAdapter};
use ReflectionException;

/**
 * Main class of Descolar.
 * Should load all the necessary adapters and run the application.
 */
class App
{

    use ErrorHandlerAdapter;
    use RouterAdapter;
    use JsonBuilderAdapter;
    use EventAdapter;
    use EnvAdapter;
    use OrmAdapter;
    use MailAdapter;

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

    public static function setUserUuid(String $userUuid): void
    {
        $_SESSION['userUuid'] = $userUuid;
    }

    public static function getUserUuid(): ?string
    {
        return  $_SESSION['userUuid'] ?? null;
    }
}