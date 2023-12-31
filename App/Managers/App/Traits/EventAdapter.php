<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Annotation\AnnotationManager;
use Descolar\Managers\Event\Annotations\Listener;
use Descolar\Managers\Event\Exceptions\EventNotFoundException;
use Descolar\Managers\Event\Interfaces\IEmitter;
use ReflectionException;

trait EventAdapter
{
    use BaseAdapter;

    private static ?IEmitter $_emitter = null;

    /**
     * Set the emitter adapter to be used by the application.
     *
     * @param class-string<IEmitter> $emitterClazz the emitter class.
     * @throws EventNotFoundException if the emitter is not found or if he doesn't extend {@see IEmitter} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useEvent(string $emitterClazz): void
    {
        self::useAdapter(self::$_emitter, EventNotFoundException::class, IEmitter::class, $emitterClazz);
    }

    /**
     * Return the emitter, if it is set, from adapters.
     *
     * @return IEmitter|null the emitter.
     * @throws EventNotFoundException if the emitter is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getEventManager(): ?IEmitter
    {
        return self::getAdapter(self::$_emitter, EventNotFoundException::class);
    }

    /**
     * Load all Listener attributes from the Endpoints directory.
     *
     * @throws ReflectionException
     */
    private static function manageEvent(): void
    {
        $ENDPOINT_DIR = DIR_ROOT . "/App/Endpoints";
        $routerAnnotation = new AnnotationManager(Listener::class);
        $routerAnnotation->generateAttributes($ENDPOINT_DIR);
    }

}