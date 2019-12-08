<?php
namespace App\Email;

use Swift_Mailer;
use Twig\Environment as Twig_Environment;

abstract class AbstractMailer
{
    protected const MAIL_FROM = 'api_platform@symfony.local';

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    // abstract public function send(...$args): ?int;
}
