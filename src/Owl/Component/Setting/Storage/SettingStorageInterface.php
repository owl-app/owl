<?php

declare(strict_types=1);

namespace Owl\Component\Setting\Storage;

interface SettingStorageInterface
{
    public function getBySectionAndKeys(string $sectionName, array $keys): array;

    public function saveValues(string $sectionName, array $values, ?array $existingSettings = [], string $lang = 'pl'): void;

    public function loadBySection(string $sectionName): array;
}
