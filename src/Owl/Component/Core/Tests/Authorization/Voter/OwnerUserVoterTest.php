<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization\Voter;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Owl\Component\Core\Authorization\Voter\OwnerUserVoter;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\Authorization\OwnerableUserInterface;
use Owl\Component\Core\Model\AdminUserInterface;

class OwnerUserVoterTest extends TestCase
{
    private OwnerUserVoter $voter;
    private AdminUserContextInterface&MockObject $adminUserContext;
    private TokenInterface&MockObject $token;

    protected function setUp(): void
    {
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->voter = new OwnerUserVoter($this->adminUserContext);
    }

    public function testSupportsWithOwnerableUserAndAdminUser(): void
    {
        $subject = $this->createMock(OwnerableUserInterface::class);
        $this->adminUserContext->method('isUser')->willReturn(true);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject);

        $this->assertTrue($result);
    }

    public function testSupportsWithNonOwnerableUser(): void
    {
        $subject = $this->createMock(AdminUserInterface::class);
        $this->adminUserContext->method('isUser')->willReturn(true);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject);

        $this->assertFalse($result);
    }

    public function testSupportsWithOwnerableUserButNotAdminUser(): void
    {
        $subject = $this->createMock(OwnerableUserInterface::class);
        $this->adminUserContext->method('isUser')->willReturn(false);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject);

        $this->assertFalse($result);
    }

    public function testVoteOnAttributeWithSameUser(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('getId')->willReturn(42);
        
        $ownerUser = $this->createMock(AdminUserInterface::class);
        $ownerUser->method('getId')->willReturn(42);
        
        $subject = $this->createMock(OwnerableUserInterface::class);
        $subject->method('getUser')->willReturn($ownerUser);
        
        $this->adminUserContext->method('getUser')->willReturn($adminUser);
        
        $reflection = new \ReflectionMethod($this->voter, 'voteOnAttribute');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject, $this->token);
        
        $this->assertTrue($result);
    }
    
    public function testVoteOnAttributeWithDifferentUser(): void
    {
        $adminUser = $this->createMock(AdminUserInterface::class);
        $adminUser->method('getId')->willReturn(42);
        
        $ownerUser = $this->createMock(AdminUserInterface::class);
        $ownerUser->method('getId')->willReturn(24);
        
        $subject = $this->createMock(OwnerableUserInterface::class);
        $subject->method('getUser')->willReturn($ownerUser);
        
        $this->adminUserContext->method('getUser')->willReturn($adminUser);
        
        $reflection = new \ReflectionMethod($this->voter, 'voteOnAttribute');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject, $this->token);
        
        $this->assertFalse($result);
    }
}
