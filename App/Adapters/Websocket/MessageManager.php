<?php

namespace Descolar\Adapters\Websocket;

use Descolar\Adapters\Websocket\Components\AbstractComponent;
use Descolar\Adapters\Websocket\Components\PrivateMessageBuilder;
use Descolar\Managers\Websocket\Interfaces\ISocketBuilder;
use Ratchet\App;
use ReflectionClass;

class MessageManager implements ISocketBuilder
{
    private ?App $_app = null;
    private ?MessageManager $_instance = null;

    private function build(string $componentName = PrivateMessageBuilder::class) : AbstractComponent
    {
        $component = new ReflectionClass($componentName);
        if(!$component->isSubclassOf(AbstractComponent::class)) {
            throw new \Exception("The component must be a subclass of AbstractComponent");
        }

        return $component->newInstance();
    }

    #[\Override] function run(int $port = 8080): void
    {
        if($this->_app === null) {
            $this->_app = new App('localhost', $port);
        }

        $this->_app->run();
    }

    #[\Override] function add(string $route, ?string $componentName = PrivateMessageBuilder::class): void
    {
        $component = $this->build($componentName);

        $this->_app->route($route, $component, ['*']);
    }

    public function getInstance(): self
    {
        if($this->_instance === null) {
            $this->_instance = new self();
        }

        return $this->_instance;
    }
}