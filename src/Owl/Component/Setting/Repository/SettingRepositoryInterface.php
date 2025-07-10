<?php

declare(strict_types=1);

namespace Owl\Component\Setting\Repository;

use Owl\Component\Setting\Model\SettingInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SettingRepositoryInterface extends RepositoryInterface
{
    /**
     * @return SettingInterface[]
     */
    public function finAllBySection(string $section): array;

    /**
     * @param string[] $keys
     *
     * @return SettingInterface[]
     */
    public function finAllBySectionAndKeys(string $section, array $keys): array;
}
