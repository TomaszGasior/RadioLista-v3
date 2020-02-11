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

    public function testRehashPasswordFromOldService(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->method('getEncoderName')
            ->willReturn('rl_v1')
        ;
        $user
            ->expects($this->once())
            ->method('setPlainPassword')
            ->with($this->equalTo(self::PLAIN_PASSWORD))
        ;

        /** @var EntityManagerInterface|MockObject */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->once())
            ->method('flush')
            ->with($this->equalTo($user))
        ;

        /** @var TokenInterface|MockObject */
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn($user)
        ;
        $token
            ->method('getCredentials')
            ->willReturn(self::PLAIN_PASSWORD)
        ;
        $token
            ->expects($this->once())
            ->method('eraseCredentials')
        ;

        $securityEvent = new InteractiveLoginEvent(new Request, $token);

        $subscriber = new RehashRLv1UserPassword($entityManager);
        $subscriber->onSecurityInteractiveLogin($securityEvent);
    }

    public function testDoNotRehashPassword(): void
    {
        $user = $this->createMock(User::class);
        $user
            ->method('getEncoderName')
            ->willReturn(null)
        ;
        $user
            ->expects($this->never())
            ->method('setPlainPassword')
        ;

        /** @var EntityManagerInterface|MockObject */
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        /** @var TokenInterface|MockObject */
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn($user)
        ;
        $token
            ->method('getCredentials')
            ->willReturn(self::PLAIN_PASSWORD)
        ;
        $token
            ->expects($this->once())
            ->method('eraseCredentials')
        ;

        $securityEvent = new InteractiveLoginEvent(new Request, $token);

        $subscriber = new RehashRLv1UserPassword($entityManager);
        $subscriber->onSecurityInteractiveLogin($securityEvent);
    }
}
