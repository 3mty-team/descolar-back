<?php

namespace Descolar\Adapters\Error;

use _PHPStan_6b522806f\Nette\Neon\Exception;
use Descolar\Adapters\Error\Handlers\CustomHandler;
use Descolar\App;
use Descolar\Managers\Error\Interfaces\IErrorManager;
use Override;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Inspector\InspectorFactory;
use Whoops\Run;

class ErrorManager implements IErrorManager
{

    private function selectHandler(): CustomHandler
    {
        $inspector = (new InspectorFactory())->create(new Exception());
        if (App::getEnvManager()->get('env') === 'DEV') {
            $handler = new CustomHandler(new PrettyPageHandler);
            $handler->getHandler()->setPageTitle("Descolar Error");
            $handler->getHandler()->setEditor("idea");
        } else {
            $handler = new CustomHandler(new JsonResponseHandler);
        }

        $handler->setInspector($inspector);
        return $handler;
    }

    #[Override] public function manage(): void
    {
        $whoops = new Run;

        $handler = $this->selectHandler();

        $whoops->pushHandler($handler->getHandler());

        $whoops->sendHttpCode($handler->getException()->getCode());
        $whoops->register();

    }

}