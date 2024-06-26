<?php

namespace Descolar\Managers\Env;

use Descolar\App;
use Descolar\Managers\Env\Interfaces\IEnv;

class EnvReader
{
    /**
     * Get the instance of the EnvManager
     *
     * @return IEnv|null The instance of the EnvManager
     */
    public static function getInstance(string $filePath = ""): ?IEnv
    {
        return App::getEnvManager()->setFilePath($filePath);
    }
}