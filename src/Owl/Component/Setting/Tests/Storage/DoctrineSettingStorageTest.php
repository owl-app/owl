<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Setting\Tests\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Setting\Model\SettingInterface;
use Owl\Component\Setting\Repository\SettingRepositoryInterface;
use Owl\Component\Setting\Storage\DoctrineSettingStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DoctrineSettingStorageTest extends TestCase
{
    private DoctrineSettingStorage $storage;

    private string $settingClass;

    private SettingRepositoryInterface&MockObject $repository;

    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        $this->settingClass = 'Owl\Component\Setting\Model\Setting';
        $this->repository = $this->createMock(SettingRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->storage = new DoctrineSettingStorage(
            $this->settingClass,
            $this->repository,
            $this->entityManager,
        );
    }

    public function testGetBySectionAndKeysWithExistingSettings(): void
    {
        $section = 'general';
        $keys = ['site_name', 'site_description'];

        $setting1 = $this->createMock(SettingInterface::class);
        $setting1->method('getName')->willReturn('site_name');
        $setting1->method('getValue')->willReturn('My Site');

        $setting2 = $this->createMock(SettingInterface::class);
        $setting2->method('getName')->willReturn('site_description');
        $setting2->method('getValue')->willReturn('My site description');

        $settings = [$setting1, $setting2];

        $this->repository->method('finAllBySectionAndKeys')
            ->with($section, $keys)
            ->willReturn($settings);

        $result = $this->storage->getBySectionAndKeys($section, $keys);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('site_name', $result);
        $this->assertArrayHasKey('site_description', $result);
        $this->assertEquals('My Site', $result['site_name']);
        $this->assertEquals('My site description', $result['site_description']);
    }

    public function testGetBySectionAndKeysWithEmptyResult(): void
    {
        $section = 'nonexistent';
        $keys = ['nonexistent_key'];

        $this->repository->method('finAllBySectionAndKeys')
            ->with($section, $keys)
            ->willReturn([]);

        $result = $this->storage->getBySectionAndKeys($section, $keys);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testSaveValuesWithExistingSettings(): void
    {
        $section = 'general';
        $values = ['site_name' => 'Updated Site Name'];

        $existingSetting = $this->createMock(SettingInterface::class);
        $existingSetting->method('getValue')->willReturn('Old Site Name');
        $existingSetting->expects($this->once())->method('setValue')->with('Updated Site Name');
        $existingSetting->expects($this->once())->method('setUpdatedAt');

        $existingSettings = ['site_name' => $existingSetting];

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->storage->saveValues($section, $values, $existingSettings);
    }

    public function testSaveValuesWithNoChanges(): void
    {
        $section = 'general';
        $values = ['site_name' => 'My Site'];

        $existingSetting = $this->createMock(SettingInterface::class);
        $existingSetting->method('getValue')->willReturn('My Site');
        $existingSetting->expects($this->never())->method('setValue');
        $existingSetting->expects($this->never())->method('setUpdatedAt');

        $existingSettings = ['site_name' => $existingSetting];

        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->storage->saveValues($section, $values, $existingSettings);
    }

    public function testSaveValuesWithNewSetting(): void
    {
        $section = 'general';
        $values = ['new_setting' => 'New Value'];
        $lang = 'pl';

        $existingSettings = [];

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->storage->saveValues($section, $values, $existingSettings, $lang);
    }

    public function testSaveValuesWithMixedSettingsAndCustomLang(): void
    {
        $section = 'general';
        $values = [
            'existing_setting' => 'Updated Value',
            'new_setting' => 'New Value',
        ];
        $lang = 'en';

        $existingSetting = $this->createMock(SettingInterface::class);
        $existingSetting->method('getValue')->willReturn('Old Value');
        $existingSetting->expects($this->once())->method('setValue')->with('Updated Value');
        $existingSetting->expects($this->once())->method('setUpdatedAt');

        $existingSettings = ['existing_setting' => $existingSetting];

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        $this->storage->saveValues($section, $values, $existingSettings, $lang);
    }

    public function testSaveValuesWithNullExistingSettings(): void
    {
        $section = 'general';
        $values = ['site_name' => 'My Site'];

        $setting = $this->createMock(SettingInterface::class);
        $setting->method('getName')->willReturn('site_name');

        $this->repository->method('finAllBySection')
            ->with($section)
            ->willReturn([$setting]);

        $this->storage->saveValues($section, $values, null);
    }

    public function testLoadBySection(): void
    {
        $section = 'general';

        $setting1 = $this->createMock(SettingInterface::class);
        $setting1->method('getName')->willReturn('site_name');

        $setting2 = $this->createMock(SettingInterface::class);
        $setting2->method('getName')->willReturn('site_description');

        $settings = [$setting1, $setting2];

        $this->repository->method('finAllBySection')
            ->with($section)
            ->willReturn($settings);

        $result = $this->storage->loadBySection($section);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('site_name', $result);
        $this->assertArrayHasKey('site_description', $result);
        $this->assertSame($setting1, $result['site_name']);
        $this->assertSame($setting2, $result['site_description']);
    }

    public function testLoadBySectionWithEmptyResult(): void
    {
        $section = 'nonexistent';

        $this->repository->method('finAllBySection')
            ->with($section)
            ->willReturn([]);

        $result = $this->storage->loadBySection($section);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
