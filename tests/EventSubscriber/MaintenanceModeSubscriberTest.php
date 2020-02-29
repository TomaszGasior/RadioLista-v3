<?php

namespace App\Tests\EventSubscriber;

use App\EventSubscriber\MaintenanceModeSubscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Environment;

class MaintenanceModeSubscriberTest extends TestCase
{
    private $lockFilePath;

    /** @var Security|MockObject */
    private $security;

    /** @var Environment|MockObject */
    private $twig;

    /** @var RequestEvent|MockObject */
    private $event;

    public function setUp(): void
    {
        $this->lockFilePath = tempnam(sys_get_temp_dir(), 'rltest');

        $this->security = $this->createMock(Security::class);

        $this->twig = $this->createMock(Environment::class);

        /** @var HttpKernelInterface|MockObject */
        $kernel = $this->createMock(HttpKernelInterface::class);
        $this->event = new RequestEvent($kernel, new Request, null);
    }

    public function testDisabledMaintenanceMode(): void
    {
        $response = new Response;

        $this->event->setResponse($response);

        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
        }

        $subscriber = new MaintenanceModeSubscriber($this->lockFilePath, $this->security, $this->twig);
        $subscriber->onKernelRequest($this->event);

        $this->assertSame($response, $this->event->getResponse());
        $this->assertEquals(200, $this->event->getResponse()->getStatusCode());
    }

    public function testEnabledMaintenanceMode(): void
    {
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(false)
        ;

        $response = new Response;

        $this->event->setResponse($response);

        file_put_contents($this->lockFilePath, '');

        $subscriber = new MaintenanceModeSubscriber($this->lockFilePath, $this->security, $this->twig);
        $subscriber->onKernelRequest($this->event);

        $this->assertNotSame($response, $this->event->getResponse());
        $this->assertEquals(503, $this->event->getResponse()->getStatusCode());
    }

    public function testEnabledMaintenanceModeAdminAccess(): void
    {
        $this->security
            ->expects($this->once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true)
        ;

        $response = new Response;

        $this->event->setResponse($response);

        file_put_contents($this->lockFilePath, '');

        $subscriber = new MaintenanceModeSubscriber($this->lockFilePath, $this->security, $this->twig);
        $subscriber->onKernelRequest($this->event);

        $this->assertSame($response, $this->event->getResponse());
        $this->assertEquals(200, $this->event->getResponse()->getStatusCode());
    }
}
