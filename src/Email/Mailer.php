<?php
namespace App\Email;

use App\Entity\User;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User $user
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function sendConfirmationEmail(User $user): void
    {
        $body = $this->twig->render('email/confirmation.html.twig', ['user' =>  $user]);

        $message = (new \Swift_Message('Api Platform - Confirm registration'))
            ->setFrom('api_platform@symfony.local')
            ->setTo($user->getEmail(), $user->getName().' '.$user->getFullname())
            ->setBody($body, 'text/html')
        ;

        $this->mailer->send($message);
    }
}
