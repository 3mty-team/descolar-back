<?php

namespace Descolar\Managers\Event;

use Descolar\App;
use Override;

class Emitter implements Interfaces\IEmitter
{
    #[Override] public static function fire(string $event, mixed $params): void
    {
            App::getEventManager()->fire($event, $params);
    }
}