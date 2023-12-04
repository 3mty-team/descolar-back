<?php


use Descolar\Adapters\Router\RouterRetriever;
use Descolar\App;

require __DIR__ . '/vendor/autoload.php';
const DIR_ROOT = __DIR__;

App::useRouter(RouterRetriever::class);

App::run();