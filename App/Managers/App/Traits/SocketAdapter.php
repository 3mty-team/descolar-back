<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Websocket\Exceptions\SocketBuilderNotFound;
use Descolar\Managers\Websocket\Interfaces\ISocketBuilder;

trait SocketAdapter
{

    use BaseAdapter;

    private static ?ISocketBuilder $_socket = null;

    /**
     * Set the SocketBuilder adapter to be used by the application.
     *
     * @param class-string<ISocketBuilder> $socketClazz the SocketBuilder class.
     * @throws SocketBuilderNotFound if the SocketBuilder is not found, or if he doesn't extend {@see ISocketBuilder} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useSocket(string $socketClazz): void
    {
        self::useAdapter(self::$_socket, SocketBuilderNotFound::class, ISocketBuilder::class, $socketClazz);
    }

    /**
     * Return the SocketBuilder if it is set, from adapters.
     *
     * @return ISocketBuilder|null the SocketBuilder.
     * @throws SocketBuilderNotFound if the SocketBuilder is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getSocketManager(): ?ISocketBuilder
    {
        return self::getAdapter(self::$_socket, SocketBuilderNotFound::class);
    }

}