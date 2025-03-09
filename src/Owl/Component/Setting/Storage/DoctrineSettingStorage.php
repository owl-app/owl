<?php

declare(strict_types=1);

namespace Owl\Component\Setting\Storage;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Setting\Model\SettingInterface;
use Owl\Component\Setting\Repository\SettingRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @property SettingRepositoryInterface $repositorySetting
 */
class DoctrineSettingStorage implements SettingStorageInterface
{
    public function __construct(
        private string $settingClass,
        private RepositoryInterface $repositorySetting,
        private EntityManagerInterface $settingManager,
    ) {
    }

    public function getBySectionAndKeys(string $sectionName, array $keys): array
    {
        $existingSettingsWithKey = [];
        $existSettings = $this->repositorySetting->finAllBySectionAndKeys($sectionName, $keys);

        foreach ($existSettings as $setting) {
            $existingSettingsWithKey[$setting->getName()] = $setting->getValue();
        }

        return $existingSettingsWithKey;
    }

    public function saveValues(string $sectionName, array $values, ?array $existingSettings = [], string $lang = 'pl'): void
    {
        if (null === $existingSettings) {
            $existingSettings = $this->loadBySection($sectionName);
        }

        foreach ($values as $settingName => $value) {
            if (array_key_exists($settingName, $existingSettings)) {
                /** @var SettingInterface $existSetting */
                $existSetting = $existingSettings[$settingName];

                if ($existSetting->getValue() === $value) {
                    continue;
                }

                $existSetting->setValue($value);
                $existSetting->setUpdatedAt(new \DateTime());
            } else {
                /** @var SettingInterface $newSetting */
                $newSetting = new $this->settingClass();
                $newSetting->setSection($sectionName);
                $newSetting->setName($settingName);
                $newSetting->setValue($value);
                $newSetting->setLang($lang);

                $this->settingManager->persist($newSetting);
            }
        }

        $this->settingManager->flush();
    }

    public function loadBySection(string $sectionName): array
    {
        $existingSettingsWithKey = [];
        $existSettings = $this->repositorySetting->finAllBySection($sectionName);

        /** @var SettingInterface $setting */
        foreach ($existSettings as $setting) {
            $existingSettingsWithKey[$setting->getName()] = $setting;
        }

        return $existingSettingsWithKey;
    }
}
