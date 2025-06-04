<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\AdminUser;
use Owl\Component\Core\Model\AdminUserInterface;
use PHPUnit\Framework\TestCase;

final class AdminUserTest extends TestCase
{
    private AdminUser $adminUser;

    protected function setUp(): void
    {
        $this->adminUser = new AdminUser();
    }

    public function testImplementsAdminUserInterface(): void
    {
        self::assertInstanceOf(AdminUserInterface::class, $this->adminUser);
    }

    public function testDisplayNameIsMutable(): void
    {
        $this->adminUser->setDisplayName('John Doe');
        self::assertSame('John Doe', $this->adminUser->getDisplayName());
    }

    public function testFirstNameIsMutable(): void
    {
        $this->adminUser->setFirstName('John');
        self::assertSame('John', $this->adminUser->getFirstName());
    }

    public function testLastNameIsMutable(): void
    {
        $this->adminUser->setLastName('Doe');
        self::assertSame('Doe', $this->adminUser->getLastName());
    }

    public function testPhoneIsMutable(): void
    {
        $this->adminUser->setPhone('123456789');
        self::assertSame('123456789', $this->adminUser->getPhone());
    }

    public function testNoteIsMutable(): void
    {
        $this->adminUser->setNote('Some note');
        self::assertSame('Some note', $this->adminUser->getNote());
    }

    public function testLocaleCodeIsMutable(): void
    {
        $this->adminUser->setLocaleCode('pl');
        self::assertSame('pl', $this->adminUser->getLocaleCode());
    }

    public function testPermissionsAreMutable(): void
    {
        $permissions = ['perm1', 'perm2'];
        $this->adminUser->setPermissions($permissions);
        self::assertSame($permissions, $this->adminUser->getPermissions());
    }

    public function testPreferencesAreMutable(): void
    {
        $preferences = ['theme' => 'dark'];
        $this->adminUser->setPreferences($preferences);
        self::assertSame($preferences, $this->adminUser->getPreferences());
    }
}
