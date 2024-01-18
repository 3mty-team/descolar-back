<?php

namespace Descolar\Managers\Event;

use Descolar\App;
use Descolar\Managers\Annotation\AnnotationManager;
use Descolar\Managers\Event\Annotations\Listener;
use Override;

class Emitter implements Interfaces\IEmitter
{
    #[Override] public static function fire(string $event, mixed $params): void
    {
            App::getEventManager()->fire($event, $params);
    }
}