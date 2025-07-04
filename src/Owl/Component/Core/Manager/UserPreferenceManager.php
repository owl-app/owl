<?php

declare(strict_types=1);

namespace Owl\Component\Core\Manager;

use Doctrine\Persistence\ObjectManager;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;

final class UserPreferenceManager implements UserPreferenceManagerInterface
{
    public function __construct(private AdminUserContextInterface $adminUserContext, private ObjectManager $manager)
    {
    }

    public function update(string $key, mixed $value): void
    {
        /** @var AdminUserInterface|null $user */
        $user = $this->adminUserContext->getUser();

        if (null === $user) {
            return;
        }

        $preferences = $user->getPreferences();
        $this->setNestedValue($preferences, $key, $value);
        $user->setPreferences($preferences);

        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function get(string $key): mixed
    {
        return $this->getNestedValue($this->getUserPreferences(), $key) ?? null;
    }

    public function has(string $key): bool
    {
        return !empty($this->getNestedValue($this->getUserPreferences(), $key));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getUserPreferences(): ?array
    {
        /** @var AdminUserInterface|null $user */
        $user = $this->adminUserContext->getUser();

        if (null === $user) {
            return null;
        }

        return $user->getPreferences();
    }

    /**
     * @param array<mixed>|null $array
     */
    private function setNestedValue(?array &$array, string $keyPath, mixed $value): void
    {
        if (!is_array($array)) {
            $array = [];
        }

        $keys = explode('.', $keyPath);
        $current = &$array;

        foreach ($keys as $key) {
            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }
            $current = &$current[$key];
        }

        $current = $value;
    }

    /**
     * @param array<mixed>|null $array
     */
    private function getNestedValue(?array $array, string $keyPath, mixed $default = null): mixed
    {
        if (!is_array($array)) {
            return $default;
        }

        $keys = explode('.', $keyPath);
        $current = $array;

        foreach ($keys as $key) {
            if (!array_key_exists($key, $current)) {
                return $default;
            }

            $current = $current[$key];

            if (!is_array($current) && $key !== end($keys)) {
                return $default;
            }
        }

        return $current;
    }
}