<?php

declare(strict_types=1);

namespace Owl\Component\Category\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class Category implements CategoryInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string */
    protected $name;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

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
}
