<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Setting\Tests\Model;

use Owl\Component\Setting\Model\Setting;
use Owl\Component\Setting\Model\SettingInterface;
use PHPUnit\Framework\TestCase;

final class SettingTest extends TestCase
{
    private Setting $setting;

    protected function setUp(): void
    {
        $this->setting = new Setting();
    }

    public function testImplementsSettingInterface(): void
    {
        self::assertInstanceOf(SettingInterface::class, $this->setting);
    }

    public function testHasNoIdByDefault(): void
    {
        self::assertNull($this->setting->getId());
    }

    public function testHasNoSectionByDefault(): void
    {
        self::expectException(\TypeError::class);
        $this->setting->getSection();
    }

    public function testItsSectionIsMutable(): void
    {
        $this->setting->setSection('general');
        self::assertSame('general', $this->setting->getSection());
    }

    public function testHasNoNameByDefault(): void
    {
        self::expectException(\TypeError::class);
        $this->setting->getName();
    }

    public function testItsNameIsMutable(): void
    {
        $this->setting->setName('site_title');
        self::assertSame('site_title', $this->setting->getName());
    }

    public function testHasNoValueByDefault(): void
    {
        self::assertNull($this->setting->getValue());
    }

    public function testItsValueIsMutable(): void
    {
        $this->setting->setValue('My Awesome Site');
        self::assertSame('My Awesome Site', $this->setting->getValue());
    }

    public function testHasNoLangByDefault(): void
    {
        self::expectException(\TypeError::class);
        $this->setting->getLang();
    }

    public function testItsLangIsMutable(): void
    {
        $this->setting->setLang('pl');
        self::assertSame('pl', $this->setting->getLang());
    }

    public function testHasCreatedAtTimestampByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->setting->getCreatedAt());
    }

    public function testHasNoUpdatedAtTimestampByDefault(): void
    {
        self::assertNull($this->setting->getUpdatedAt());
    }

    public function testItsUpdatedAtTimestampIsMutable(): void
    {
        $dateTime = new \DateTime();
        $this->setting->setUpdatedAt($dateTime);
        self::assertSame($dateTime, $this->setting->getUpdatedAt());
    }
}
