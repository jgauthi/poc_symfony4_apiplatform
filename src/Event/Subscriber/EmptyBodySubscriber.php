<?php
namespace App\Event\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['handleEmptyBody', EventPriorities::POST_DESERIALIZE]
        ];
    }

    /**
     * @param GetResponseEvent $event
     * @throws EmptyBodyException
     */
    public function handleEmptyBody(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        $route = $request->get('_route');

        if (!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT]) ||
            in_array($request->getContentType(), ['html', 'form']) ||
            substr($route, 0, 3) !== 'api'
        ) {
            return;
        }

        $data = $request->get('data');
        if (null === $data) {
            throw new EmptyBodyException();
        }
    }
}
