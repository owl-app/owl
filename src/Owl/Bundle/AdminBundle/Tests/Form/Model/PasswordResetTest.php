<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\Model;

use Owl\Bundle\AdminBundle\Form\Model\PasswordReset;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordReset::class)]
final class PasswordResetTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $passwordReset = new PasswordReset();
        
        $this->assertInstanceOf(PasswordReset::class, $passwordReset);
    }

    #[Test]
    public function it_has_null_password_by_default(): void
    {
        $passwordReset = new PasswordReset();
        
        $this->assertNull($passwordReset->getPassword());
    }

    #[Test]
    public function it_can_set_and_get_password(): void
    {
        $password = 'new-secure-password';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
    }

    #[Test]
    public function it_can_set_null_password(): void
    {
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword('some-password');
        
        $passwordReset->setPassword(null);
        
        $this->assertNull($passwordReset->getPassword());
    }

    #[Test]
    public function it_can_set_empty_string_password(): void
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

    #[Test]
    #[DataProvider('passwordProvider')]
    public function it_handles_various_password_formats(string $password): void
    {
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
    }

    #[Test]
    public function it_allows_password_to_be_overwritten(): void
    {
        $initialPassword = 'initial-password';
        $newPassword = 'new-password';
        
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword($initialPassword);
        
        $this->assertSame($initialPassword, $passwordReset->getPassword());
        
        $passwordReset->setPassword($newPassword);
        
        $this->assertSame($newPassword, $passwordReset->getPassword());
    }

    #[Test]
    public function it_maintains_password_state(): void
    {
        $password = 'test-password';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        // Multiple calls should return the same value
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertSame($password, $passwordReset->getPassword());
    }

    #[Test]
    public function it_handles_password_with_only_whitespace(): void
    {
        $password = '   ';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
    }

    #[Test]
    public function it_does_not_trim_password(): void
    {
        $password = '  password  ';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertStringStartsWith('  ', $passwordReset->getPassword());
        $this->assertStringEndsWith('  ', $passwordReset->getPassword());
    }

    #[Test]
    public function it_preserves_password_case(): void
    {
        $password = 'PaSsWoRd123';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertNotSame(strtolower($password), $passwordReset->getPassword());
        $this->assertNotSame(strtoupper($password), $passwordReset->getPassword());
    }

    #[Test]
    public function it_handles_zero_as_password(): void
    {
        $password = '0';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
        $this->assertNotNull($passwordReset->getPassword());
    }

    #[Test]
    public function it_handles_false_string_as_password(): void
    {
        $password = 'false';
        $passwordReset = new PasswordReset();
        
        $passwordReset->setPassword($password);
        
        $this->assertSame($password, $passwordReset->getPassword());
    }

    #[Test]
    public function it_can_reset_password_to_null_after_being_set(): void
    {
        $passwordReset = new PasswordReset();
        $passwordReset->setPassword('initial-password');
        
        $this->assertNotNull($passwordReset->getPassword());
        
        $passwordReset->setPassword(null);
        
        $this->assertNull($passwordReset->getPassword());
    }
}
