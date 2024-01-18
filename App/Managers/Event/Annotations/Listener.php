<?php

namespace Descolar\Managers\Event\Annotations;

use Attribute;
use Descolar\Managers\Event\Interfaces\IListener;
use Override;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Listener implements IListener
{
    /**
     * @param string $name The name of the listener
     */
    public function __construct(
        private string $name
    )
    {
    }

    #[Override] public function getName(): string
    {
        return $this->name;
    }
}