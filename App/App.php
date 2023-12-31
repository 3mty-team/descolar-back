<?php

namespace Descolar;

use Descolar\Managers\Router\Router;
use Descolar\Managers\App\Traits\{RouterAdapter, SwaggerAdapter, EventAdapter};
use ReflectionException;

/**
 * Main class of Descolar.
 * Should load all the necessary adapters and run the application.
 */
class App
{

    use RouterAdapter;
    use SwaggerAdapter;
    use EventAdapter;


    /**
     * Start the application and run the router.
     *
     * @throws ReflectionException
     */
    public static function run(): void
    {
        self::manageRouter();
        self::manageEvent();
        Router::getInstance()->listen();
    }
}