<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\Setting;
use PHPUnit\Framework\TestCase;

final class SettingTest extends TestCase
{
    private Setting $setting;

    protected function setUp(): void
    {
        $this->setting = new Setting();
    }

    public function testDescriptionLoginPageIsMutable(): void
    {
        $this->setting->setDescriptionLoginPage('Login description');
        self::assertSame('Login description', $this->setting->getDescriptionLoginPage());
    }

    public function testDescriptionDashboardIsMutable(): void
    {
        $this->setting->setDescriptionDashboard('Dashboard description');
        self::assertSame('Dashboard description', $this->setting->getDescriptionDashboard());
    }
}
