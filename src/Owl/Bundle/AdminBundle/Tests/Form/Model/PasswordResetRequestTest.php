<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Form\Model;

use Owl\Bundle\AdminBundle\Form\Model\PasswordResetRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PasswordResetRequestTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $this->assertInstanceOf(PasswordResetRequest::class, $passwordResetRequest);
    }

    public function testHasNullEmailByDefault(): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $this->assertNull($passwordResetRequest->getEmail());
    }

    public function testCanSetAndGetEmail(): void
    {
        $email = 'test@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testCanSetNullEmail(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('test@example.com');

        $passwordResetRequest->setEmail(null);

        $this->assertNull($passwordResetRequest->getEmail());
    }

    public function testCanSetEmptyStringEmail(): void
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

    #[DataProvider('emailProvider')]
    public function testHandlesVariousEmailFormats(string $email): void
    {
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testAllowsEmailToBeOverwritten(): void
    {
        $initialEmail = 'initial@example.com';
        $newEmail = 'new@example.com';

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($initialEmail);

        $this->assertSame($initialEmail, $passwordResetRequest->getEmail());

        $passwordResetRequest->setEmail($newEmail);

        $this->assertSame($newEmail, $passwordResetRequest->getEmail());
    }

    public function testMaintainsEmailState(): void
    {
        $email = 'test@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        // Multiple calls should return the same value
        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithWhitespace(): void
    {
        $email = '  test@example.com  ';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testDoesNotTrimEmail(): void
    {
        $email = '  test@example.com  ';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertStringStartsWith('  ', $passwordResetRequest->getEmail());
        $this->assertStringEndsWith('  ', $passwordResetRequest->getEmail());
    }

    public function testPreservesEmailCase(): void
    {
        $email = 'Test@Example.Com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
        $this->assertNotSame(strtolower($email), $passwordResetRequest->getEmail());
        $this->assertNotSame(strtoupper($email), $passwordResetRequest->getEmail());
    }

    public function testHandlesInvalidEmailFormat(): void
    {
        $invalidEmail = 'not-an-email';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($invalidEmail);

        $this->assertSame($invalidEmail, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithSpecialCharacters(): void
    {
        $email = 'user+tag@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testCanResetEmailToNullAfterBeingSet(): void
    {
        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail('test@example.com');

        $this->assertNotNull($passwordResetRequest->getEmail());

        $passwordResetRequest->setEmail(null);

        $this->assertNull($passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithOnlyWhitespace(): void
    {
        $email = '   ';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithNewlines(): void
    {
        $email = "test@example.com\n";
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithTabs(): void
    {
        $email = "test@example.com\t";
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesVeryLongEmail(): void
    {
        $localPart = str_repeat('a', 64);
        $domain = str_repeat('b', 63) . '.com';
        $email = $localPart . '@' . $domain;

        $passwordResetRequest = new PasswordResetRequest();
        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithMultipleAtSigns(): void
    {
        $email = 'user@@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailStartingWithDot(): void
    {
        $email = '.user@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailEndingWithDot(): void
    {
        $email = 'user.@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithConsecutiveDots(): void
    {
        $email = 'user..name@example.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithoutDomain(): void
    {
        $email = 'user@';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }

    public function testHandlesEmailWithoutAtSign(): void
    {
        $email = 'userexample.com';
        $passwordResetRequest = new PasswordResetRequest();

        $passwordResetRequest->setEmail($email);

        $this->assertSame($email, $passwordResetRequest->getEmail());
    }
}
