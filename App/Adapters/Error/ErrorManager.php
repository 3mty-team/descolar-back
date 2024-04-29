<?php

namespace Descolar\Adapters\Error;

use Descolar\App;
use Descolar\Managers\Error\Interfaces\IErrorManager;
use Override;
use Throwable;
use Whoops\Exception\Inspector;
use Whoops\Handler\Handler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class ErrorManager implements IErrorManager
{

    private function selectHandler(): Handler
    {
        if (App::getEnvManager()->get('env') === 'DEV') {
            $handler = new PrettyPageHandler;
            $handler->setPageTitle("Descolar Error");
            $handler->setEditor("idea");
        } else {
            $handler = new JsonResponseHandler;
        }

        return $handler;
    }

    #[Override] public function manage(): void
    {
        $whoops = new Run;

        $handler = $this->selectHandler();

        $whoops->pushHandler($handler);

        $whoops->pushHandler(function (Throwable $exception, Inspector $inspector, Run $run) {
            $code = ($exception->getCode() >= 400 && $exception->getCode() < 600 ) ? $exception->getCode() : 400;
            $run->sendHttpCode($code);
        });

        $whoops->register();
    }

}