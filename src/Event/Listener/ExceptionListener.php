<?php
namespace App\Event\Listener;

use ApiPlatform\Core\Exception\{InvalidArgumentException, ItemNotFoundException};
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\Validator\{ConstraintViolation, ConstraintViolationList};

class ExceptionListener extends ErrorListener
{
    /**
     * @param ExceptionEvent $event
     * @param string|null $eventName
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function onKernelException(ExceptionEvent $event, string $eventName = null, EventDispatcherInterface $eventDispatcher = null): void
    {
        $request = $event->getRequest();
        // Normalize exceptions only for routes managed by API Platform
        if (
            'html' === $request->getRequestFormat('') ||
            (!$request->attributes->has('_api_resource_class') && !$request->attributes->has('_api_respond') && !$request->attributes->has('_graphql'))
        ) {
            return;
        }

        $exception = $event->getThrowable();
        if ($exception instanceof InvalidArgumentException && $exception->getPrevious() instanceof ItemNotFoundException) {
            $violations = new ConstraintViolationList([
                new ConstraintViolation(
                    $exception->getMessage(),
                    null,
                    [],
                    '',
                    '',
                    ''
                )
            ]);

            $event->setThrowable( new ValidationException($violations) );
            return;
        }
    }
}
