<?php
namespace App\Email;

use App\Entity\User;
use Swift_Message;

class UserConfirmationMailer extends AbstractMailer
{
    /**
     * @param User $user
     * @return int
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(User $user): int
    {
        $body = $this->twig->render('email/confirmation.html.twig', ['user' =>  $user]);

        $message = (new Swift_Message('Api Platform - Confirm registration'))
            ->setFrom(self::MAIL_FROM)
            ->setTo($user->getEmail(), $user->getName().' '.$user->getFullname())
            ->setBody($body, 'text/html')
        ;

        return $this->mailer->send($message);
    }
}
