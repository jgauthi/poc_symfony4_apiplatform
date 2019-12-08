<?php
namespace App\Email;

use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment as Twig_Environment;

abstract class AbstractMailer
{
    protected const MAIL_FROM = 'api_platform@symfony.local';

    /**
     * @var MailerInterface
     */
    protected $mailer;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    public function __construct(MailerInterface $mailer, Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    // abstract public function send(...$args): ?int;
}
