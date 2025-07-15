<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\Model;

use Owl\Bundle\AdminBundle\Form\Model\PasswordReset;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PasswordResetTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $passwordReset = new PasswordReset();

        $this->assertInstanceOf(PasswordReset::class, $passwordReset);
    }

    public function testHasNullPasswordByDefault(): void
    {
        $passwordReset = new PasswordReset();

        $this->assertNull($passwordReset->getPassword());
    }

    public function testCanSetAndGetPassword(): void
    {
        $password = 'new-secure-password';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
    }

    public function testCanSetNullPassword(): void
    {
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword('some-password');

        $passwordReset->setPassword(null);

        $this->assertNull($passwordReset->getPassword());
    }

    public function testCanSetEmptyStringPassword(): void
    {
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword('');

        $this->assertSame('', $passwordReset->getPassword());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function passwordProvider(): array
    {
        return [
            'simple password' => ['password' => 'password123'],
            'complex password' => ['password' => 'P@ssw0rd!2024'],
            'password with spaces' => ['password' => 'password with spaces'],
            'password with special chars' => ['password' => '!@#$%^&*()_+-=[]{}|;:,.<>?'],
            'numeric password' => ['password' => '123456789'],
            'very long password' => ['password' => str_repeat('a', 100)],
            'unicode password' => ['password' => 'пароль123'],
            'password with newlines' => ['password' => "password\nwith\nnewlines"],
            'password with tabs' => ['password' => "password\twith\ttabs"],
        ];
    }

    #[DataProvider('passwordProvider')]
    public function testHandlesVariousPasswordFormats(string $password): void
    {
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
    }

    public function testAllowsPasswordToBeOverwritten(): void
    {
        $initialPassword = 'initial-password';
        $newPassword = 'new-password';

        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($initialPassword);

        $this->assertSame($initialPassword, $passwordReset->getPassword());

        $passwordReset->setPassword($newPassword);

        $this->assertSame($newPassword, $passwordReset->getPassword());
    }

    public function testMaintainsPasswordState(): void
    {
        $password = 'test-password';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        // Multiple calls should return the same value
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertSame($password, $passwordReset->getPassword());
    }

    public function testHandlesPasswordWithOnlyWhitespace(): void
    {
        $password = '   ';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
    }

    public function testDoesNotTrimPassword(): void
    {
        $password = '  password  ';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertStringStartsWith('  ', $passwordReset->getPassword());
        $this->assertStringEndsWith('  ', $passwordReset->getPassword());
    }

    public function testPreservesPasswordCase(): void
    {
        $password = 'PaSsWoRd123';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertNotSame(strtolower($password), $passwordReset->getPassword());
        $this->assertNotSame(strtoupper($password), $passwordReset->getPassword());
    }

    public function testHandlesZeroAsPassword(): void
    {
        $password = '0';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertNotNull($passwordReset->getPassword());
    }

    public function testHandlesFalseStringAsPassword(): void
    {
        $password = 'false';
        $passwordReset = new PasswordReset();

        $passwordReset->setPassword($password);

        $this->assertSame($password, $passwordReset->getPassword());
    }

    public function testCanResetPasswordToNullAfterBeingSet(): void
    {
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword('initial-password');

        $this->assertNotNull($passwordReset->getPassword());

        $passwordReset->setPassword(null);

        $this->assertNull($passwordReset->getPassword());
    }
}
