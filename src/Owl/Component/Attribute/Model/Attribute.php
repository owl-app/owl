<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Component\Attribute\Model;

use Owl\Component\Attribute\AttributeType\TextAttributeType;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Attribute implements AttributeInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string */
    protected $name;

    /** @var string */
    protected $code;

    /** @var string */
    protected $type = TextAttributeType::TYPE;

    /** @var array */
    protected $configuration = [];

    /** @var string */
    protected $storageType;

    /** @var int|null */
    protected $position;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // public function __toString(): string
    // {
    //     return (string) $this->getName();
    // }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getStorageType(): ?string
    {
        return $this->storageType;
    }

    public function setStorageType(?string $storageType): void
    {
        $this->storageType = $storageType;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }
}
