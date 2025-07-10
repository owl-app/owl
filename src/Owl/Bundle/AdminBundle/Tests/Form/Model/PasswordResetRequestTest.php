<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\Model;

use Owl\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PasswordResetRequest::class)]
final class PasswordResetRequestTest extends TestCase
{
    #[Test]
    public function it_can_be_instantiated(): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $this->assertInstanceOf(PasswordResetRequest::class, $passwordResetRequest);
    }

    #[Test]
    public function it_has_null_email_by_default(): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $this->assertNull($passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_can_set_and_get_email(): void
    {
        $email = 'test@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_can_set_null_email(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('test@example.com');

        $passwordResetRequest->setEmail(null);

        $this->assertNull($passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_can_set_empty_string_email(): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail('');

        $this->assertSame('', $passwordResetRequest->getEmail());
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function emailProvider(): array
    {
        return [
            'simple email' => ['email' => 'test@example.com'],
            'email with subdomain' => ['email' => 'user@mail.example.com'],
            'email with plus sign' => ['email' => 'user+tag@example.com'],
            'email with dots' => ['email' => 'first.last@example.com'],
            'email with numbers' => ['email' => 'user123@example123.com'],
            'email with hyphen' => ['email' => 'user-name@example-domain.com'],
            'email with underscore' => ['email' => 'user_name@example.com'],
            'long email' => ['email' => 'very.long.email.address@very-long-domain-name.example.com'],
            'short email' => ['email' => 'a@b.co'],
            'email with international domain' => ['email' => 'user@example.рф'],
            'email with unicode' => ['email' => 'тест@example.com'],
            'email with uppercase' => ['email' => 'USER@EXAMPLE.COM'],
            'email with mixed case' => ['email' => 'User@ExAmPlE.CoM'],
        ];
    }

    #[Test]
    #[DataProvider('emailProvider')]
    public function it_handles_various_email_formats(string $email): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_allows_email_to_be_overwritten(): void
    {
        $initialEmail = 'initial@example.com';
        $newEmail = 'new@example.com';

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($initialEmail);

        $this->assertSame($initialEmail, $passwordResetRequest->getEmail());

        $passwordResetRequest->setEmail($newEmail);

        $this->assertSame($newEmail, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_maintains_email_state(): void
    {
        $email = 'test@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        // Multiple calls should return the same value
        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_whitespace(): void
    {
        $email = '  test@example.com  ';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_does_not_trim_email(): void
    {
        $email = '  test@example.com  ';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertStringStartsWith('  ', $passwordResetRequest->getEmail());
        $this->assertStringEndsWith('  ', $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_preserves_email_case(): void
    {
        $email = 'Test@Example.Com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertNotSame(strtolower($email), $passwordResetRequest->getEmail());
        $this->assertNotSame(strtoupper($email), $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_invalid_email_format(): void
    {
        $invalidEmail = 'not-an-email';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($invalidEmail);

        $this->assertSame($invalidEmail, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_special_characters(): void
    {
        $email = 'user+tag@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_can_reset_email_to_null_after_being_set(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('test@example.com');

        $this->assertNotNull($passwordResetRequest->getEmail());

        $passwordResetRequest->setEmail(null);

        $this->assertNull($passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_only_whitespace(): void
    {
        $email = '   ';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_newlines(): void
    {
        $email = "test@example.com\n";
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_tabs(): void
    {
        $email = "test@example.com\t";
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_very_long_email(): void
    {
        $localPart = str_repeat('a', 64);
        $domain = str_repeat('b', 63) . '.com';
        $email = $localPart . '@' . $domain;

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_multiple_at_signs(): void
    {
        $email = 'user@@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_starting_with_dot(): void
    {
        $email = '.user@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_ending_with_dot(): void
    {
        $email = 'user.@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_with_consecutive_dots(): void
    {
        $email = 'user..name@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_without_domain(): void
    {
        $email = 'user@';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    #[Test]
    public function it_handles_email_without_at_sign(): void
    {
        $email = 'userexample.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }
}
