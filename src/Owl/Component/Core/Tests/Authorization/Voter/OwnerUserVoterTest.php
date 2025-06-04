<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization\Voter;

use Owl\Component\Core\Authorization\Voter\OwnerUserVoter;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\Authorization\OwnerableUserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class OwnerUserVoterTest extends TestCase
{
    private OwnerUserVoter $voter;

    private AdminUserContextInterface&MockObject $adminUserContext;

    private TokenInterface&MockObject $token;

    private AdminUserInterface&MockObject $currentUser;

    private AdminUserInterface&MockObject $otherUser;

    private OwnerableUserInterface&MockObject $ownerableSubject;

    protected function setUp(): void
    {
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->currentUser = $this->createMock(AdminUserInterface::class);
        $this->otherUser = $this->createMock(AdminUserInterface::class);
        $this->ownerableSubject = $this->createMock(OwnerableUserInterface::class);

        $this->voter = new OwnerUserVoter($this->adminUserContext);
    }

    public function testItGrantsAccessWhenUserIsOwner(): void
    {
        $this->adminUserContext
            ->method('isUser')
            ->willReturn(true);

        $this->adminUserContext
            ->method('getUser')
            ->willReturn($this->currentUser);

        $this->currentUser
            ->method('getId')
            ->willReturn(1);

        $this->ownerableSubject
            ->method('getUser')
            ->willReturn($this->currentUser);

        $result = $this->voter->vote($this->token, $this->ownerableSubject, ['EDIT']);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testItDeniesAccessWhenUserIsNotOwner(): void
    {
        $this->adminUserContext
            ->method('isUser')
            ->willReturn(true);

        $this->adminUserContext
            ->method('getUser')
            ->willReturn($this->currentUser);

        $this->currentUser
            ->method('getId')
            ->willReturn(1);

        $this->otherUser
            ->method('getId')
            ->willReturn(2);

        $this->ownerableSubject
            ->method('getUser')
            ->willReturn($this->otherUser);

        $result = $this->voter->vote($this->token, $this->ownerableSubject, ['EDIT']);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testItAbstainsIfSubjectIsNotOwnerableOrUserIsNotStandardUser(): void
    {
        $this->adminUserContext
            ->method('isUser')
            ->willReturn(false);

        $subject = new stdClass();

        $result = $this->voter->vote($this->token, $subject, ['EDIT']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }
}
