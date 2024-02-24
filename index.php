<?php

/**
 * This is the main file of the project, it is responsible for starting the application.
 * Please, do not change this file.
 *
 * @author Descolar Team <development@descolar.fr>
 */

use Descolar\Adapters\Env\EnvManager;
use Descolar\Adapters\Error\ErrorManager;
use Descolar\Adapters\Event\EventReader;
use Descolar\Adapters\JsonBuilder\JsonBuilderManager;
use Descolar\Adapters\Orm\OrmManager;
use Descolar\Adapters\Router\RouterRetriever;
use Descolar\Adapters\Swagger\SwaggerManager;
use Descolar\App;

/**
 * The autoloader of the project
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * The root directory of the project
 */
const DIR_ROOT = __DIR__;

/**
 * Load Adapters
 */
App::useErrorHandler(ErrorManager::class);
App::useEnv(EnvManager::class);
App::useOrm(OrmManager::class);
App::useRouter(RouterRetriever::class);
App::useSwagger(SwaggerManager::class);
App::useEvent(EventReader::class);
App::useJsonBuilder(JsonBuilderManager::class);

/**
 * Run the application
 */
App::run();