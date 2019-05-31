<?php
namespace App\Event\Listener;

use ApiPlatform\Core\Exception\{InvalidArgumentException, ItemNotFoundException};
use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener as BaseExceptionListener;
use Symfony\Component\Validator\{ConstraintViolation, ConstraintViolationList};

class ExceptionListener extends BaseExceptionListener
{
    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $request = $event->getRequest();
        // Normalize exceptions only for routes managed by API Platform
        if (
            'html' === $request->getRequestFormat('') ||
            (!$request->attributes->has('_api_resource_class') && !$request->attributes->has('_api_respond') && !$request->attributes->has('_graphql'))
        ) {
            return;
        }

        $exception = $event->getException();
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

            $event->setException( new ValidationException($violations) );
            return;
        }
    }
}
