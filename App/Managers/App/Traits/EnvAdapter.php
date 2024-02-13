<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Env\Exceptions\EnvManagerNotFoundException;
use Descolar\Managers\Env\Interfaces\IEnv;

trait EnvAdapter
{
    use BaseAdapter;

    private static ?IEnv $_env = null;

    /**
     * Set the env adapter to be used by the application.
     *
     * @param class-string<IEnv> $envClazz the env class.
     * @throws EnvManagerNotFoundException if the env is not found or if he doesn't extend {@see IEnv} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useEnv(string $envClazz): void
    {
        self::useAdapter(self::$_env, EnvManagerNotFoundException::class, IEnv::class, $envClazz);
    }

    /**
     * Return the env, if it is set, from adapters.
     *
     * @return IEnv|null the env.
     * @throws EnvManagerNotFoundException if the env is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getEnvManager(): ?IEnv
    {
        return self::getAdapter(self::$_env, EnvManagerNotFoundException::class);
    }

}