<?php

namespace Descolar;

use Descolar\Managers\Error\ErrorHandler;
use Descolar\Managers\Router\Router;
use Descolar\Managers\App\Traits\{EnvAdapter,
    ErrorHandlerAdapter,
    JsonBuilderAdapter,
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


    /**
     * Start the application
     *
     * @throws ReflectionException
     */
    public static function run(): void
    {
        ErrorHandler::handle();
        self::manageRouter();
        self::manageEvent();
        Router::getInstance()->listen();
    }
}