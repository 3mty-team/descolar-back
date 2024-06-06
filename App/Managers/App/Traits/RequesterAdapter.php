<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Requester\Exceptions\RequesterNotFoundException;
use Descolar\Managers\Requester\Interfaces\IRequest;

trait RequesterAdapter
{
    use BaseAdapter;

    private static ?IRequest $_requester = null;

    /**
     * Set the Requester adapter to be used by the application.
     *
     * @param class-string<IRequest> $requesterClazz the Requester class.
     * @throws RequesterNotFoundException if the Requester is not found or if he doesn't extend {@see IRequest} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useRequester(string $requesterClazz): void
    {
        self::useAdapter(self::$_requester, RequesterNotFoundException::class, IRequest::class, $requesterClazz);
    }

    /**
     * Return the Requester, if it is set, from adapters.
     *
     * @return IRequest|null the Requester.
     * @throws RequesterNotFoundException if the Requester is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getRequester(): ?IRequest
    {
        return self::getAdapter(self::$_requester, RequesterNotFoundException::class);
    }

}