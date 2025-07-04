<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization\Voter;

use Owl\Component\Core\Authorization\Voter\AdminSystemResourceVoter;
use Owl\Component\Core\Context\AdminUserContextInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class AdminSystemResourceVoterTest extends TestCase
{
    private AdminSystemResourceVoter $voter;

    private AdminUserContextInterface&MockObject $adminUserContext;

    private TokenInterface&MockObject $token;

    private ResourceInterface&MockObject $resourceSubject;

    protected function setUp(): void
    {
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->resourceSubject = $this->createMock(ResourceInterface::class);

        $this->voter = new AdminSystemResourceVoter($this->adminUserContext);
    }

    public function testItGrantsAccessIfUserIsAdminAndSubjectIsResource(): void
    {
        $this->adminUserContext->method('isAdminSystem')->willReturn(true);
        $result = $this->voter->vote($this->token, $this->resourceSubject, ['ANY_ATTRIBUTE']);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testItAbstainsIfUserIsNotAdmin(): void
    {
        $this->adminUserContext->method('isAdminSystem')->willReturn(false);
        $result = $this->voter->vote($this->token, $this->resourceSubject, ['ANY_ATTRIBUTE']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testItAbstainsIfSubjectIsNotAResource(): void
    {
        $subject = new stdClass();

        $result = $this->voter->vote($this->token, $subject, ['ANY_ATTRIBUTE']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }
}