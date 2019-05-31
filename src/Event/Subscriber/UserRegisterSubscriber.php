<?php
// Api Platform: User password encoder
namespace App\Event\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Email\Mailer;
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
     * @var Mailer
     */
    private $mailer;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator, Mailer $mailer)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
        ];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function userRegistered(GetResponseForControllerResultEvent $event): void
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || !in_array($method, [Request::METHOD_POST])) {
            return;
        }

        $user->setPassword( $this->passwordEncoder->encodePassword($user, $user->getPassword()) );
        $user->setConfirmationToken( $this->tokenGenerator->getRandomSecureToken() );

        // Send token by email
        $this->mailer->sendConfirmationEmail($user);
    }
}
