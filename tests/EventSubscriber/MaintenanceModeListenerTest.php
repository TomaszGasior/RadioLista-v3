<?php

namespace App\Tests\EventSubscriber;

use App\EventListener\MaintenanceModeListener;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

class MaintenanceModeListenerTest extends TestCase
{
    private $lockFilePath;

    private $security;

    private $twig;

    private $event;

    public function setUp(): void
    {
        $this->lockFilePath = tempnam(sys_get_temp_dir(), 'rltest');
        $this->security = $this->createStub(Security::class);
        $this->twig = $this->createStub(Environment::class);

        $kernel = $this->createStub(HttpKernelInterface::class);

        $this->event = new RequestEvent($kernel, new Request, HttpKernelInterface::MAIN_REQUEST);
    }

    public function test_does_not_interrupt_response_when_maintenance_mode_is_disabled(): void
    {
        $response = new Response;

        $this->event->setResponse($response);

        if (file_exists($this->lockFilePath)) {
            unlink($this->lockFilePath);
        }

        $subscriber = new MaintenanceModeListener($this->lockFilePath, $this->security, $this->twig);
        $subscriber->onKernelRequest($this->event);

        $this->assertSame($response, $this->event->getResponse());
        $this->assertEquals(200, $this->event->getResponse()->getStatusCode());
    }

    public function test_interrupts_response_when_maintenance_mode_is_enabled(): void
    {
        $this->security
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(false)
        ;

        $response = new Response;

        $this->event->setResponse($response);

        file_put_contents($this->lockFilePath, '');

        $subscriber = new MaintenanceModeListener($this->lockFilePath, $this->security, $this->twig);
        $subscriber->onKernelRequest($this->event);

        $this->assertNotSame($response, $this->event->getResponse());
        $this->assertEquals(503, $this->event->getResponse()->getStatusCode());
    }

    public function test_does_not_interrupt_response_when_maintenance_mode_is_enabled_but_user_is_administrator(): void
    {
        $this->security
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(true)
        ;

        $response = new Response;

        $this->event->setResponse($response);

        file_put_contents($this->lockFilePath, '');

        $subscriber = new MaintenanceModeListener($this->lockFilePath, $this->security, $this->twig);
        $subscriber->onKernelRequest($this->event);

        $this->assertSame($response, $this->event->getResponse());
        $this->assertEquals(200, $this->event->getResponse()->getStatusCode());
    }
}
