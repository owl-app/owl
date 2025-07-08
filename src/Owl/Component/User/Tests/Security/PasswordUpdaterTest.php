<?php

declare(strict_types=1);

namespace Tests\Owl\Component\User\Security;

use Owl\Component\User\Model\CredentialsHolderInterface;
use Owl\Component\User\Security\PasswordUpdater;
use Owl\Component\User\Security\PasswordUpdaterInterface;
use Owl\Component\User\Security\UserPasswordHasherInterface;
use PHPUnit\Framework\TestCase;

final class PasswordUpdaterTest extends TestCase
{
    private UserPasswordHasherInterface $passwordHasher;
    private PasswordUpdater $passwordUpdater;

    protected function setUp(): void
    {
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->passwordUpdater = new PasswordUpdater($this->passwordHasher);
    }

    public function testImplementsPasswordUpdaterInterface(): void
    {
        self::assertInstanceOf(PasswordUpdaterInterface::class, $this->passwordUpdater);
    }

    public function testUpdatePasswordWithPlainPassword(): void
    {
        $user = $this->createMock(CredentialsHolderInterface::class);
        
        $user->expects(self::once())
            ->method('getPlainPassword')
            ->willReturn('plain_password');

        $this->passwordHasher->expects(self::once())
            ->method('hash')
            ->with($user)
            ->willReturn('hashed_password');

        $user->expects(self::once())
            ->method('setPassword')
            ->with('hashed_password');

        $user->expects(self::once())
            ->method('eraseCredentials');

        $this->passwordUpdater->updatePassword($user);
    }

    public function testUpdatePasswordWithEmptyPlainPassword(): void
    {
        $user = $this->createMock(CredentialsHolderInterface::class);
        
        $user->expects(self::once())
            ->method('getPlainPassword')
            ->willReturn('');

        $this->passwordHasher->expects(self::never())
            ->method('hash');

        $user->expects(self::never())
            ->method('setPassword');

        $user->expects(self::never())
            ->method('eraseCredentials');

        $this->passwordUpdater->updatePassword($user);
    }

    public function testUpdatePasswordWithNullPlainPassword(): void
    {
        $user = $this->createMock(CredentialsHolderInterface::class);
        
        $user->expects(self::once())
            ->method('getPlainPassword')
            ->willReturn(null);

        $this->passwordHasher->expects(self::never())
            ->method('hash');

        $user->expects(self::never())
            ->method('setPassword');

        $user->expects(self::never())
            ->method('eraseCredentials');

        $this->passwordUpdater->updatePassword($user);
    }

    public function testUpdatePasswordWithWhitespacePlainPassword(): void
    {
        $user = $this->createMock(CredentialsHolderInterface::class);
        
        $user->expects(self::once())
            ->method('getPlainPassword')
            ->willReturn('   ');

        $this->passwordHasher->expects(self::once())
            ->method('hash')
            ->with($user)
            ->willReturn('hashed_whitespace_password');

        $user->expects(self::once())
            ->method('setPassword')
            ->with('hashed_whitespace_password');

        $user->expects(self::once())
            ->method('eraseCredentials');

        $this->passwordUpdater->updatePassword($user);
    }

    public function testUpdatePasswordWithZeroAsPlainPassword(): void
    {
        $user = $this->createMock(CredentialsHolderInterface::class);
        
        $user->expects(self::once())
            ->method('getPlainPassword')
            ->willReturn('0');

        $this->passwordHasher->expects(self::once())
            ->method('hash')
            ->with($user)
            ->willReturn('hashed_zero_password');

        $user->expects(self::once())
            ->method('setPassword')
            ->with('hashed_zero_password');

        $user->expects(self::once())
            ->method('eraseCredentials');

        $this->passwordUpdater->updatePassword($user);
    }
}
