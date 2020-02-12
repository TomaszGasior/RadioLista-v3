<?php

namespace App\Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\RehashRLv1UserPassword;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class RehashRLv1UserPasswordTest extends TestCase
{
    private const PLAIN_PASSWORD = 'Passw0rd!';

    /** @var User|MockObject */
    private $user;

    /** @var EntityManagerInterface|MockObject */
    private $entityManager;

    /** @var TokenInterface|MockObject */
    private $token;

    public function setUp(): void
    {
        $this->user = $this->createMock(User::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->token = $this->createMock(TokenInterface::class);
        $this->token
            ->method('getUser')
            ->willReturn($this->user)
        ;
        $this->token
            ->method('getCredentials')
            ->willReturn(self::PLAIN_PASSWORD)
        ;
        $this->token
            ->expects($this->once())
            ->method('eraseCredentials')
        ;
    }

    public function testRehashPasswordFromOldService(): void
    {
        $this->user
            ->method('getEncoderName')
            ->willReturn('rl_v1')
        ;
        $this->user
            ->expects($this->once())
            ->method('setPlainPassword')
            ->with($this->equalTo(self::PLAIN_PASSWORD))
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        $securityEvent = new InteractiveLoginEvent(new Request, $this->token);

        $subscriber = new RehashRLv1UserPassword($this->entityManager);
        $subscriber->onSecurityInteractiveLogin($securityEvent);
    }

    public function testDoNotRehashPassword(): void
    {
        $this->user
            ->method('getEncoderName')
            ->willReturn(null)
        ;
        $this->user
            ->expects($this->never())
            ->method('setPlainPassword')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        $securityEvent = new InteractiveLoginEvent(new Request, $this->token);

        $subscriber = new RehashRLv1UserPassword($this->entityManager);
        $subscriber->onSecurityInteractiveLogin($securityEvent);
    }
}
