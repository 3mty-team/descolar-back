<?php

namespace Descolar\Managers\App\Traits;

use Descolar\Managers\Mail\Exceptions\MailConnectorNotFound;
use Descolar\Managers\Mail\Interfaces\IMailBuilder;

trait MailAdapter
{
    use BaseAdapter;

    private static ?IMailBuilder $_mail = null;

    /**
     * Set the MailBuilder adapter to be used by the application.
     *
     * @param class-string<IMailBuilder> $mailClazz the MailBuilder class.
     * @throws MailConnectorNotFound if the MailBuilder is not found, or if he doesn't extend {@see IMailBuilder} interface.
     *
     * @uses BaseAdapter::useAdapter()
     */
    public static function useMail(string $mailClazz): void
    {
        self::useAdapter(self::$_mail, MailConnectorNotFound::class, IMailBuilder::class, $mailClazz);
    }

    /**
     * Return the MailBuilder if it is set, from adapters.
     *
     * @return IMailBuilder|null the MailBuilder.
     * @throws MailConnectorNotFound if the MailBuilder is not set.
     *
     * @uses BaseAdapter::getAdapter()
     */
    public static function getMailBuilder(): ?IMailBuilder
    {
        return self::getAdapter(self::$_mail, MailConnectorNotFound::class);
    }

}