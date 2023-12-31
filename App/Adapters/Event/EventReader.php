<?php

namespace Descolar\Adapters\Event;

use Descolar\Managers\Annotation\AnnotationManager;
use Descolar\Managers\Event\Annotations\Listener;
use Descolar\Managers\Event\Interfaces\IEmitter;
use ReflectionException;
use ReflectionMethod;

class EventReader implements IEmitter
{

    /**
     * @inheritDoc
     *
     * @throws ReflectionException if the listener is not found.
     */
    public static function fire(string $event, mixed $params): void
    {
        $listeners = (new AnnotationManager(Listener::class))->getAttributeList();
        if (empty($listeners)) {
            return;
        }

        /**
         * @var $attribute Listener
         * @var $method ReflectionMethod
         */
        foreach ($listeners as [$listener, $method]) {

            /**
             * @var $instance Listener
             */
            if ($listener->getName() === $event) {
                $controller = $method->getDeclaringClass()->getMethod("getInstance")->invoke(null);
                $method->invoke($controller, $params);
            }
        }
    }
}