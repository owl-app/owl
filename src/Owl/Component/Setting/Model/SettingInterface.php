<?php

declare(strict_types=1);

namespace Owl\Component\Setting\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface SettingInterface extends TimestampableInterface, ResourceInterface
{
    public function getSection(): string;

    public function setSection(?string $section): void;

    public function getName(): string;

    public function setName(?string $name): void;

    public function getValue(): string;

    public function setValue(?string $value): void;

    public function getLang(): string;

    public function setLang(?string $lang): void;
}
