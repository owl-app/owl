<?php

declare(strict_types=1);

namespace Tests\Owl\Component\User\Security\Checker;

use Owl\Component\User\Security\Checker\TokenUniquenessChecker;
use Owl\Component\User\Security\Checker\UniquenessCheckerInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class TokenUniquenessCheckerTest extends TestCase
{
    private RepositoryInterface $repository;
    private TokenUniquenessChecker $checker;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(RepositoryInterface::class);
        $this->checker = new TokenUniquenessChecker($this->repository, 'token');
    }

    public function testImplementsUniquenessCheckerInterface(): void
    {
        self::assertInstanceOf(UniquenessCheckerInterface::class, $this->checker);
    }

    public function testIsUniqueReturnsTrueWhenTokenNotFound(): void
    {
        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['token' => 'unique_token'])
            ->willReturn(null);

        $result = $this->checker->isUnique('unique_token');

        self::assertTrue($result);
    }

    public function testIsUniqueReturnsFalseWhenTokenExists(): void
    {
        $existingEntity = new \stdClass();

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['token' => 'existing_token'])
            ->willReturn($existingEntity);

        $result = $this->checker->isUnique('existing_token');

        self::assertFalse($result);
    }

    public function testIsUniqueWithDifferentFieldName(): void
    {
        $checker = new TokenUniquenessChecker($this->repository, 'resetToken');

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['resetToken' => 'test_token'])
            ->willReturn(null);

        $result = $checker->isUnique('test_token');

        self::assertTrue($result);
    }

    public function testIsUniqueWithEmptyToken(): void
    {
        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['token' => ''])
            ->willReturn(null);

        $result = $this->checker->isUnique('');

        self::assertTrue($result);
    }

    public function testIsUniqueWithNumericToken(): void
    {
        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['token' => '12345'])
            ->willReturn(null);

        $result = $this->checker->isUnique('12345');

        self::assertTrue($result);
    }

    public function testIsUniqueWithSpecialCharacters(): void
    {
        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['token' => 'token-with_special.chars'])
            ->willReturn(null);

        $result = $this->checker->isUnique('token-with_special.chars');

        self::assertTrue($result);
    }

    public function testIsUniqueWithLongToken(): void
    {
        $longToken = str_repeat('a', 1000);

        $this->repository->expects(self::once())
            ->method('findOneBy')
            ->with(['token' => $longToken])
            ->willReturn(null);

        $result = $this->checker->isUnique($longToken);

        self::assertTrue($result);
    }

    public function testConstructorWithDifferentFieldNames(): void
    {
        $passwordResetChecker = new TokenUniquenessChecker($this->repository, 'passwordResetToken');
        $emailVerificationChecker = new TokenUniquenessChecker($this->repository, 'emailVerificationToken');

        self::assertInstanceOf(TokenUniquenessChecker::class, $passwordResetChecker);
        self::assertInstanceOf(TokenUniquenessChecker::class, $emailVerificationChecker);
    }
}
