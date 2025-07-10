<?php

declare(strict_types=1);

namespace Owl\Component\Setting\Storage;

interface SettingStorageInterface
{
    /**
     * @param string[] $keys
     *
     * @return array<string, mixed>
     */
    public function getBySectionAndKeys(string $sectionName, array $keys): array;

    /**
     * @param array<string, mixed> $values
     * @param array<string, mixed> $existingSettings
     */
    public function saveValues(string $sectionName, array $values, array $existingSettings = [], string $lang = 'pl'): void;

    /**
     * @return array<string, mixed>
     */
    public function loadBySection(string $sectionName): array;
}
