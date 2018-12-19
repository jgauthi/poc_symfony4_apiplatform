<?php
// Api Platform: User password encoder
namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegisterSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator, \Swift_Mailer $mailer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
        ];
    }

    public function userRegistered(GetResponseForControllerResultEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        $user->setPassword( $this->passwordEncoder->encodePassword($user, $user->getPassword()) );
        $user->setConfirmationToken( $this->tokenGenerator->getRandomSecureToken() );

        // Send token by email
        $message = (new \Swift_Message('Api Platform - Confirm registration'))
            ->setFrom('api_platform@symfony.local')
            ->setTo($user->getEmail(), $user->getName().' '.$user->getFullname())
            ->setBody('Hello here...')
        ;

        $this->mailer->send($message);
    }
}
