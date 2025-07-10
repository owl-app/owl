<?php

declare(strict_types=1);

namespace Tests\Owl\Component\User\Security\Generator;

use Owl\Component\User\Security\Checker\UniquenessCheckerInterface;
use Owl\Component\User\Security\Generator\GeneratorInterface;
use Owl\Component\User\Security\Generator\UniquePinGenerator;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Generator\RandomnessGeneratorInterface;

final class UniquePinGeneratorTest extends TestCase
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
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            4,
        );

        self::assertInstanceOf(GeneratorInterface::class, $generator);
    }

    public function testConstructorWithValidPinLength(): void
    {
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            6,
        );

        self::assertInstanceOf(UniquePinGenerator::class, $generator);
    }

    public function testConstructorThrowsExceptionForInvalidPinLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of token length has to be at least 1.');

        new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            0,
        );
    }

    public function testConstructorThrowsExceptionForNegativePinLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The value of token length has to be at least 1.');

        new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            -1,
        );
    }

    public function testGenerateReturnsUniquePin(): void
    {
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            4,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateNumeric')
            ->with(4)
            ->willReturn('1234');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('1234')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('1234', $result);
    }

    public function testGenerateRetriesUntilUniquePinIsFound(): void
    {
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            4,
        );

        $this->randomnessGenerator->expects(self::exactly(3))
            ->method('generateNumeric')
            ->with(4)
            ->willReturnOnConsecutiveCalls('1234', '5678', '9012');

        $this->uniquenessChecker->expects(self::exactly(3))
            ->method('isUnique')
            ->willReturnCallback(function ($pin) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    self::assertSame('1234', $pin);

                    return false;
                }
                if ($callCount === 2) {
                    self::assertSame('5678', $pin);

                    return false;
                }
                if ($callCount === 3) {
                    self::assertSame('9012', $pin);

                    return true;
                }

                throw new \Exception('Unexpected call');
            });

        $result = $generator->generate();

        self::assertSame('9012', $result);
    }

    public function testGenerateWithDifferentPinLength(): void
    {
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            6,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateNumeric')
            ->with(6)
            ->willReturn('123456');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('123456')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('123456', $result);
    }

    public function testGenerateWithMinimumPinLength(): void
    {
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            1,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateNumeric')
            ->with(1)
            ->willReturn('7');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('7')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('7', $result);
    }

    public function testGenerateWithLongPinLength(): void
    {
        $generator = new UniquePinGenerator(
            $this->randomnessGenerator,
            $this->uniquenessChecker,
            10,
        );

        $this->randomnessGenerator->expects(self::once())
            ->method('generateNumeric')
            ->with(10)
            ->willReturn('1234567890');

        $this->uniquenessChecker->expects(self::once())
            ->method('isUnique')
            ->with('1234567890')
            ->willReturn(true);

        $result = $generator->generate();

        self::assertSame('1234567890', $result);
    }
}
