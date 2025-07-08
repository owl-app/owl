<?php

declare(strict_types=1);

namespace Tests\Owl\Component\User\Model;

use Owl\Component\User\Model\CredentialsHolderInterface;
use Owl\Component\User\Model\User;
use Owl\Component\User\Model\UserInterface;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use SyliusLabs\Polyfill\Symfony\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testImplementsRequiredInterfaces(): void
    {
        self::assertInstanceOf(UserInterface::class, $this->user);
        self::assertInstanceOf(PasswordAuthenticatedUserInterface::class, $this->user);
        self::assertInstanceOf(AdvancedUserInterface::class, $this->user);
        self::assertInstanceOf(CredentialsHolderInterface::class, $this->user);
        self::assertInstanceOf(ResourceInterface::class, $this->user);
        self::assertInstanceOf(\Serializable::class, $this->user);
        self::assertInstanceOf(TimestampableInterface::class, $this->user);
        self::assertInstanceOf(ToggleableInterface::class, $this->user);
        self::assertInstanceOf(PasswordHasherAwareInterface::class, $this->user);
    }

    public function testInitialState(): void
    {
        self::assertNotNull($this->user->getCreatedAt());
        self::assertFalse($this->user->isEnabled());
        self::assertSame([], $this->user->getRoles());
        self::assertNull($this->user->getId());
        self::assertNull($this->user->getEmail());
        self::assertNull($this->user->getPassword());
        self::assertNull($this->user->getPlainPassword());
        self::assertNull($this->user->getLastLogin());
        self::assertNull($this->user->getEmailVerificationToken());
        self::assertNull($this->user->getPasswordResetToken());
        self::assertNull($this->user->getPasswordRequestedAt());
        self::assertNull($this->user->getVerifiedAt());
        self::assertFalse($this->user->isVerified());
        self::assertTrue($this->user->isAccountNonLocked());
        self::assertNull($this->user->getExpiresAt());
        self::assertNull($this->user->getCredentialsExpireAt());
        self::assertNull($this->user->getSalt());
        self::assertNull($this->user->getPasswordHasherName());
    }

    public function testEmailIsMutable(): void
    {
        $this->user->setEmail('test@example.com');
        self::assertSame('test@example.com', $this->user->getEmail());
        self::assertSame('test@example.com', $this->user->getUsername());
        self::assertSame('test@example.com', $this->user->getUserIdentifier());
    }

    public function testPasswordIsMutable(): void
    {
        $this->user->setPassword('encoded_password');
        self::assertSame('encoded_password', $this->user->getPassword());
    }

    public function testPlainPasswordIsMutable(): void
    {
        $this->user->setPlainPassword('plain_password');
        self::assertSame('plain_password', $this->user->getPlainPassword());
    }

    public function testEraseCredentials(): void
    {
        $this->user->setPlainPassword('plain_password');
        $this->user->eraseCredentials();
        self::assertNull($this->user->getPlainPassword());
    }

    public function testLastLoginIsMutable(): void
    {
        $date = new \DateTime();
        $this->user->setLastLogin($date);
        self::assertSame($date, $this->user->getLastLogin());
    }

    public function testEmailVerificationTokenIsMutable(): void
    {
        $this->user->setEmailVerificationToken('verification_token');
        self::assertSame('verification_token', $this->user->getEmailVerificationToken());
    }

    public function testPasswordResetTokenIsMutable(): void
    {
        $this->user->setPasswordResetToken('reset_token');
        self::assertSame('reset_token', $this->user->getPasswordResetToken());
    }

    public function testPasswordRequestedAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->user->setPasswordRequestedAt($date);
        self::assertSame($date, $this->user->getPasswordRequestedAt());
    }

    public function testVerifiedAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->user->setVerifiedAt($date);
        self::assertSame($date, $this->user->getVerifiedAt());
        self::assertTrue($this->user->isVerified());
    }

    public function testIsVerifiedReturnsFalseWhenVerifiedAtIsNull(): void
    {
        $this->user->setVerifiedAt(null);
        self::assertFalse($this->user->isVerified());
    }

    public function testExpiresAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->user->setExpiresAt($date);
        self::assertSame($date, $this->user->getExpiresAt());
    }

    public function testCredentialsExpireAtIsMutable(): void
    {
        $date = new \DateTime();
        $this->user->setCredentialsExpireAt($date);
        self::assertSame($date, $this->user->getCredentialsExpireAt());
    }

    public function testLockedIsMutable(): void
    {
        $this->user->setLocked(true);
        self::assertFalse($this->user->isAccountNonLocked());

        $this->user->setLocked(false);
        self::assertTrue($this->user->isAccountNonLocked());
    }

    public function testPasswordHasherNameIsMutable(): void
    {
        $this->user->setPasswordHasherName('bcrypt');
        self::assertSame('bcrypt', $this->user->getPasswordHasherName());
    }

    public function testIsAccountNonExpiredWithNullExpiresAt(): void
    {
        $this->user->setExpiresAt(null);
        self::assertTrue($this->user->isAccountNonExpired());
    }

    public function testIsAccountNonExpiredWithFutureDate(): void
    {
        $futureDate = new \DateTime('+1 day');
        $this->user->setExpiresAt($futureDate);
        self::assertTrue($this->user->isAccountNonExpired());
    }

    public function testIsAccountNonExpiredWithPastDate(): void
    {
        $pastDate = new \DateTime('-1 day');
        $this->user->setExpiresAt($pastDate);
        self::assertFalse($this->user->isAccountNonExpired());
    }

    public function testIsCredentialsNonExpiredWithNullCredentialsExpireAt(): void
    {
        $this->user->setCredentialsExpireAt(null);
        self::assertTrue($this->user->isCredentialsNonExpired());
    }

    public function testIsCredentialsNonExpiredWithFutureDate(): void
    {
        $futureDate = new \DateTime('+1 day');
        $this->user->setCredentialsExpireAt($futureDate);
        self::assertTrue($this->user->isCredentialsNonExpired());
    }

    public function testIsCredentialsNonExpiredWithPastDate(): void
    {
        $pastDate = new \DateTime('-1 day');
        $this->user->setCredentialsExpireAt($pastDate);
        self::assertFalse($this->user->isCredentialsNonExpired());
    }

    public function testHasRole(): void
    {
        $this->user->addRole('ROLE_USER');
        self::assertTrue($this->user->hasRole('ROLE_USER'));
        self::assertTrue($this->user->hasRole('role_user')); // Case insensitive
        self::assertFalse($this->user->hasRole('ROLE_ADMIN'));
    }

    public function testAddRole(): void
    {
        $this->user->addRole('ROLE_USER');
        self::assertContains('ROLE_USER', $this->user->getRoles());

        // Adding same role twice should not duplicate
        $this->user->addRole('ROLE_USER');
        self::assertCount(1, array_filter($this->user->getRoles(), fn($role) => $role === 'ROLE_USER'));
    }

    public function testAddRoleConvertsToUppercase(): void
    {
        $this->user->addRole('role_user');
        self::assertContains('ROLE_USER', $this->user->getRoles());
        self::assertNotContains('role_user', $this->user->getRoles());
    }

    public function testRemoveRole(): void
    {
        $this->user->addRole('ROLE_USER');
        $this->user->addRole('ROLE_ADMIN');
        
        $this->user->removeRole('ROLE_USER');
        self::assertNotContains('ROLE_USER', $this->user->getRoles());
        self::assertContains('ROLE_ADMIN', $this->user->getRoles());
    }

    public function testRemoveRoleConvertsToUppercase(): void
    {
        $this->user->addRole('ROLE_USER');
        $this->user->removeRole('role_user');
        self::assertNotContains('ROLE_USER', $this->user->getRoles());
    }

    public function testRemoveNonExistentRole(): void
    {
        $this->user->addRole('ROLE_USER');
        $originalRoles = $this->user->getRoles();
        
        $this->user->removeRole('ROLE_ADMIN');
        self::assertSame($originalRoles, $this->user->getRoles());
    }

    public function testIsPasswordRequestNonExpiredWithNullPasswordRequestedAt(): void
    {
        $ttl = new \DateInterval('PT1H'); // 1 hour
        self::assertFalse($this->user->isPasswordRequestNonExpired($ttl));
    }

    public function testIsPasswordRequestNonExpiredWithinTtl(): void
    {
        $ttl = new \DateInterval('PT1H'); // 1 hour
        $requestedAt = new \DateTime('-30 minutes');
        $this->user->setPasswordRequestedAt($requestedAt);
        
        self::assertTrue($this->user->isPasswordRequestNonExpired($ttl));
    }

    public function testIsPasswordRequestNonExpiredBeyondTtl(): void
    {
        $ttl = new \DateInterval('PT1H'); // 1 hour
        $requestedAt = new \DateTime('-2 hours');
        $this->user->setPasswordRequestedAt($requestedAt);
        
        self::assertFalse($this->user->isPasswordRequestNonExpired($ttl));
    }

    public function testToStringReturnsEmail(): void
    {
        $this->user->setEmail('test@example.com');
        self::assertSame('test@example.com', (string) $this->user);
    }

    public function testToStringReturnsEmptyStringWhenEmailIsNull(): void
    {
        self::assertSame('', (string) $this->user);
    }

    public function testSerializeAndUnserialize(): void
    {
        $this->user->setEmail('test@example.com');
        $this->user->setPassword('encoded_password');
        $this->user->setLocked(true);
        $this->user->setEnabled(true);
        $this->user->setPasswordHasherName('bcrypt');

        $serialized = $this->user->serialize();
        $newUser = new User();
        $newUser->unserialize($serialized);

        self::assertSame('encoded_password', $newUser->getPassword());
        self::assertSame('test@example.com', $newUser->getEmail());
        self::assertFalse($newUser->isAccountNonLocked());
        self::assertTrue($newUser->isEnabled());
        self::assertSame('bcrypt', $newUser->getPasswordHasherName());
    }

    public function testUnserializeWithOlderDataFormat(): void
    {
        // Test backward compatibility with older serialized data
        $this->user->setEmail('test@example.com');
        $this->user->setPassword('encoded_password');
        $this->user->setLocked(false);
        $this->user->setEnabled(true);

        // Simulate older serialization format without hasherName
        $olderData = serialize([
            'encoded_password',
            'test@example.com',
            false,
            true,
            123,
        ]);

        $newUser = new User();
        $newUser->unserialize($olderData);

        self::assertSame('encoded_password', $newUser->getPassword());
        self::assertSame('test@example.com', $newUser->getEmail());
        self::assertTrue($newUser->isAccountNonLocked());
        self::assertTrue($newUser->isEnabled());
        self::assertNull($newUser->getPasswordHasherName()); // Should be null for backward compatibility
    }
}
