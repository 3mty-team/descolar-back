<?php

namespace Descolar\Managers\Mail\Interfaces;

use Closure;

interface IMailBuilder
{
    /**
     * Set the Sender of the email
     * @param string $from The email address of the sender
     * @param string $name The name of the sender
     * @return IMailBuilder
     */
    public function setFrom(string $from, string $name = ''): IMailBuilder;

    /**
     * Set the SMTP configuration
     * @param bool $bool If the email uses SMTP
     * @return IMailBuilder
     */
    public function setSMTP(?bool $bool = true): IMailBuilder;

    /**
     * Set the Recipient of the email
     * @param string $address The email address of the recipient
     * @param string $name The name of the recipient
     * @return IMailBuilder
     */
    public function addTo(string $address, string $name = ''): IMailBuilder;

    /**
     * Set the Subject of the email
     * @param string $subject The subject of the email
     * @return IMailBuilder
     */
    public function setSubject(string $subject): IMailBuilder;

    /**
     * Set the Body of the email
     * @param bool $asHTML If the body is HTML
     * @param Closure $body The body of the email
     * @return IMailBuilder
     */
    public function setBody(bool $asHTML, Closure $body): IMailBuilder;

    /**
     * Send the email
     * @return bool
     */
    public function send(): bool;

}