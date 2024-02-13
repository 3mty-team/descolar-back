<?php

namespace Descolar\Managers\Env;

use Descolar\App;
use Descolar\Managers\Env\Interfaces\IEnv;

class EnvReader
{

    public static function getInstance(): ?IEnv
    {
        return App::getEnvManager();
    }

}