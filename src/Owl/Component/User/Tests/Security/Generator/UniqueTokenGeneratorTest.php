<?php

declare(strict_types=1);

namespace Tests\Owl\Component\User\Security\Generator;

use Owl\Component\User\Security\Checker\UniquenessCheckerInterface;
use Owl\Component\User\Security\Generator\GeneratorInterface;
use Owl\Component\User\Security\Generator\UniqueTokenGenerator;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

final class UniqueTokenGeneratorTest extends TestCase
{
    private RandomnessGeneratorInterface $randomnessGenerator;

    private UniquenessCheckerInterface $uniquenessChecker;

    protected function setUp(): void
    {
        $this->randomnessGenerator = $this->createMock(RandomnessGeneratorInterface::class);
        $this->uniquenessChecker = $this->createMock(UniquenessCheckerInterface::class);
    }

    public function testImplementsGeneratorInterface(): void
    {
        $generator = new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            10,
        );

        self::assertInstanceOf(GeneratorInterface::class, $generator);
    }

    public function testConstructorWithValidTokenLength(): void
    {
        $generator = new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            10,
        );

        self::assertInstanceOf(UniqueTokenGenerator::class, $generator);
    }

    public function testConstructorThrowsExceptionForInvalidTokenLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of token length has to be at least 1.');

        new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            0,
        );
    }

    public function testConstructorThrowsExceptionForNegativeTokenLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of token length has to be at least 1.');

        new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            -1,
        );
    }

    public function testGenerateReturnsUniqueToken(): void
    {
        $generator = new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            10,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(10)
            ->willReturn('unique_token');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('unique_token')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('unique_token', $result);
    }

    public function testGenerateRetriesUntilUniqueTokenIsFound(): void
    {
        $generator = new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            10,
        );

        $this->randomnessGenerator->expects(self::exactly(3))
            ->method('generateUriSafeString')
            ->with(10)
            ->willReturnOnConsecutiveCalls('token1', 'token2', 'unique_token');

        $this->uniquenessChecker->expects(self::exactly(3))
            ->method('isUnique')
            ->willReturnCallback(function ($token) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    self::assertSame('token1', $token);

                    return false;
                }
                if ($callCount === 2) {
                    self::assertSame('token2', $token);

                    return false;
                }
                if ($callCount === 3) {
                    self::assertSame('unique_token', $token);

                    return true;
                }

                throw new \Exception('Unexpected call');
            });

        $result = $generator->generate();

        self::assertSame('unique_token', $result);
    }

    public function testGenerateWithDifferentTokenLength(): void
    {
        $generator = new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            20,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(20)
            ->willReturn('twenty_char_token__');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('twenty_char_token__')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('twenty_char_token__', $result);
    }

    public function testGenerateWithMinimumTokenLength(): void
    {
        $generator = new UniqueTokenGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            1,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateUriSafeString')
            ->with(1)
            ->willReturn('x');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('x')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('x', $result);
    }
}
