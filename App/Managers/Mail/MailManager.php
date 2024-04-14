<?php

namespace Descolar\Managers\Mail;

use Descolar\App;
use Descolar\Managers\Mail\Interfaces\IMailBuilder;

class MailManager
{

    public static function build(): IMailBuilder
    {
        return App::getMailBuilder()->build();
    }

}