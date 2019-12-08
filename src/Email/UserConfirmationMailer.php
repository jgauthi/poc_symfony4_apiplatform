<?php
namespace App\Email;

use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class UserConfirmationMailer extends AbstractMailer
{
    /**
     * @param User $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function send(User $user): void
    {
        $body = $this->twig->render('email/confirmation.html.twig', ['user' =>  $user]);

        $email = (new Email)
            ->from(self::MAIL_FROM)
            ->subject('Api Platform - Confirm registration')
            ->to( new Address($user->getEmail(), "{$user->getName()} {$user->getFullname()}") )
            ->html($body)
            ->text(strip_tags($body))
        ;

        $this->mailer->send($email);
    }
}
