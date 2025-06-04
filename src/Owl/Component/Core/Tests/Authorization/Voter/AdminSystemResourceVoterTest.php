<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization\Voter;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Owl\Component\Core\Authorization\Voter\AdminSystemResourceVoter;
use Owl\Component\Core\Context\AdminUserContextInterface;

class AdminSystemResourceVoterTest extends TestCase
{
    private AdminSystemResourceVoter $voter;

    private AdminUserContextInterface&MockObject $adminUserContext;

    private TokenInterface&MockObject $token;

    protected function setUp(): void
    {
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->voter = new AdminSystemResourceVoter($this->adminUserContext);
    }

    public function testSupportsWithResourceAndAdminSystem(): void
    {
        $subject = $this->createMock(ResourceInterface::class);
        $this->adminUserContext->method('isAdminSystem')->willReturn(true);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject);

        $this->assertTrue($result);
    }

    public function testSupportsWithNonResource(): void
    {
        $subject = new \stdClass();
        $this->adminUserContext->method('isAdminSystem')->willReturn(true);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject);

        $this->assertFalse($result);
    }

    public function testSupportsWithResourceButNotAdminSystem(): void
    {
        $subject = $this->createMock(ResourceInterface::class);
        $this->adminUserContext->method('isAdminSystem')->willReturn(false);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject);

        $this->assertFalse($result);
    }

    public function testVoteOnAttributeAlwaysReturnsTrue(): void
    {
        $subject = $this->createMock(ResourceInterface::class);
        
        $reflection = new \ReflectionMethod($this->voter, 'voteOnAttribute');
        $result = $reflection->invoke($this->voter, 'some_attribute', $subject, $this->token);
        
        $this->assertTrue($result);
    }
}
