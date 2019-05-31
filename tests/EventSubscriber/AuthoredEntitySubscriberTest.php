<?php
namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\{BlogPost, Comment, User};
use App\Event\Subscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration(): void
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();

        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            [ 'getAuthenticatedUser',  EventPriorities::PRE_WRITE],
            $result[ KernelEvents::VIEW ]
        );
    }

    /**
     * @param string $classname
     * @param bool $shouldCallSetAuthor
     * @param string $method
     * @dataProvider providerSetAuthorCall
     */
    public function testSetAuthorCall(string $classname, bool $shouldCallSetAuthor, string $method): void
    {
        $entityMock = $this->getEntityMock($classname, $shouldCallSetAuthor);
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock($method, $entityMock);

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser($eventMock);
    }

    public function providerSetAuthorCall(): array
    {
        return [
            [BlogPost::class, true, 'POST'],
            [BlogPost::class, false, 'GET'],
            ['NonExisting', false, 'POST'],
            [Comment::class, true, 'POST'],
        ];
    }

    public function testNoTokenPresent()
    {
        $tokenStorageMock = $this->getTokenStorageMock(false);
        $eventMock = $this->getEventMock('POST', new class {});

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }

    /**
     * @param bool $hasToken
     * @return MockObject|TokenStorageInterface
     */
    private function getTokenStorageMock(bool $hasToken = true): MockObject
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();
        $tokenMock->expects( $hasToken ? $this->once() : $this->never() )
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMockForAbstractClass();
        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($hasToken ? $tokenMock : null);

        return $tokenStorageMock;
    }

    /**
     * @param string $method
     * @param object $controllerResult
     * @return MockObject|GetResponseForControllerResultEvent
     */
    private function getEventMock(string $method, object $controllerResult): MockObject
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->expects( $this->once() )
            ->method('getMethod')
            ->willReturn($method);

        $eventMock = $this->getMockBuilder(GetResponseForControllerResultEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $eventMock->expects( $this->once() )
            ->method('getControllerResult')
            ->willReturn($controllerResult);

        $eventMock->expects( $this->once() )
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }

    /**
     * @param string $className
     * @param bool $shouldCallSetAuthor
     * @return MockObject
     */
    private function getEntityMock(string $className, bool $shouldCallSetAuthor): MockObject
    {
        $entityMock = $this->getMockBuilder($className)
            ->setMethods(['setAuthor'])
            ->getMock();

        $entityMock->expects($shouldCallSetAuthor ? $this->once() : $this->never())
            ->method('setAuthor');

        return $entityMock;
    }
}
