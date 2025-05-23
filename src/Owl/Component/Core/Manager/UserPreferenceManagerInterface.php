<?php

declare(strict_types=1);

namespace Owl\Component\Core\Manager;

interface UserPreferenceManagerInterface
{
    public function update(string $key, mixed $value): void;

    public function get(string $key): mixed;

    public function has(string $key): bool;
}
