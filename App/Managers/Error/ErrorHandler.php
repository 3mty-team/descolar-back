<?php

namespace Descolar\Managers\Error;

use Descolar\App;

class ErrorHandler
{

    /**
     * Handle the error manager.
     */
    public static function handle(): void {
        App::getErrorHandler()->manage();
    }

}