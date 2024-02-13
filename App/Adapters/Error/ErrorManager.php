<?php

namespace Descolar\Adapters\Error;

use Descolar\Managers\Error\Interfaces\IErrorManager;
use Override;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class ErrorManager implements IErrorManager
{

    #[Override] public function manage(): void
    {
        $whoops = new Run;
        $whoops->pushHandler(new PrettyPageHandler);
        $whoops->register();
    }

}