<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class AuthAssignment implements AuthAssignmentInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $itemName;

    /** @var string */
    protected $userId;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getItemName(): ?string
    {
        return $this->itemName;
    }

    public function setItemName(?string $itemName): void
    {
        $this->itemName = $itemName;
    }
}
