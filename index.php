<?php

/**
 * This is the main file of the project, it is responsible for starting the application.
 * Please, do not change this file.
 *
 * @author Descolar Team <development@descolar.fr>
 */

use Descolar\App;
use Descolar\Adapters\Router\RouterRetriever;

/**
 * The autoloader of the project
 */
require __DIR__ . '/vendor/autoload.php';

/**
 * The root directory of the project
 */
const DIR_ROOT = __DIR__;

/**
 * Load Router Adapter
 */
App::useRouter(RouterRetriever::class);

/**
 * Run the application
 */
App::run();