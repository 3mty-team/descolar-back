<?php

namespace Descolar\Adapters\Mail;

use Closure;
use Descolar\Adapters\Mail\Exceptions\SMTPException;
use Descolar\App;
use Descolar\Managers\Mail\Interfaces\IMailBuilder;
use Override;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailBuilder implements IMailBuilder
{

    private static ?PHPMailer $_mailerInstance = null;

    public function build(): IMailBuilder
    {
        if (self::$_mailerInstance === null) {
            self::$_mailerInstance = new PHPMailer();
        }
        return new self();
    }

    private function getInstance(): PHPMailer
    {
        return self::$_mailerInstance;
    }

    /**
     * @throws Exception
     */
    #[Override] public function setFrom(string $from, string $name = ''): IMailBuilder
    {
        $this->getInstance()->setFrom($from, $name);

        return $this;
    }

    #[Override] public function setSMTP(?bool $bool = true): IMailBuilder
    {
        if ($bool) {

            if(
                !App::getEnvManager()->has('SMTP_HOST') ||
                !App::getEnvManager()->has('SMTP_PORT')
            ) {
                throw new SMTPException('ENV SMTP configuration is missing');
            }

            $this->getInstance()->isSMTP();
            $this->getInstance()->Host = App::getEnvManager()->get('SMTP_HOST');
            $this->getInstance()->CharSet = "UTF-8";
            $this->getInstance()->SMTPAuth = true;
            $this->getInstance()->Username = App::getEnvManager()->get('SMTP_USERNAME') ?? "";
            $this->getInstance()->Password = App::getEnvManager()->get('SMTP_PASSWORD') ?? "";
            $this->getInstance()->SMTPSecure = App::getEnvManager()->get('SMTP_ENCRYPTION') ?? "";
            $this->getInstance()->Port = App::getEnvManager()->get('SMTP_PORT');
        } else {
            $this->getInstance()->isMail();
        }

        return $this;
    }

    /**
     * @throws Exception
     */
    #[Override] public function addTo(string $address, string $name = ''): IMailBuilder
    {
        $this->getInstance()->addAddress($address, $name);

        return $this;
    }

    #[Override] public function setSubject(string $subject): IMailBuilder
    {
        $this->getInstance()->Subject = $subject;

        return $this;
    }

    #[Override] public function setBody(bool $asHTML, Closure $body): IMailBuilder
    {
        $this->getInstance()->isHTML($asHTML);
        $this->getInstance()->Body = $body();

        return $this;
    }

    /**
     * @throws Exception
     */
    #[Override] public function send(): bool
    {
        return $this->getInstance()->send();
    }
}