<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class SendMailService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(
        string $from,
        string $to,
        string $subject,
        string $template,
        array $context
    ): void
    {


        //On va créer le mail
        $email = (new TemplatedEMail())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('email/'.$template.'.html.twig')
            ->context($context);
        $this->mailer->send($email);
    }
}
