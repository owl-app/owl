<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

abstract class BaseAuthItem implements AuthItemInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var string */
    protected $groupPermission;

    /** @var string */
    protected $description;

    /** @var string */
    protected $ruleName;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    abstract public function getType(): string;

    public function getGroupPermission(): ?string
    {
        return $this->groupPermission;
    }

    public function setGroupPermission(string $groupPermission): void
    {
        $this->groupPermission = $groupPermission;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getRuleName(): ?string
    {
        return $this->ruleName;
    }

    public function setRuleName(?string $ruleName): void
    {
        $this->ruleName = $ruleName;
    }
}
