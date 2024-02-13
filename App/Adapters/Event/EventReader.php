<?php

namespace Descolar\Adapters\Event;

use Descolar\Managers\Annotation\AnnotationManager;
use Descolar\Managers\Event\Annotations\Listener;
use Descolar\Managers\Event\Interfaces\IEmitter;
use Override;
use ReflectionMethod;

class EventReader implements IEmitter
{

    #[Override] public static function fire(string $event, mixed $params): void
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