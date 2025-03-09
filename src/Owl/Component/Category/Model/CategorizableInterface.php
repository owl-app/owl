<?php

declare(strict_types=1);

namespace Owl\Component\Category\Model;

interface CategorizableInterface
{
    public function getCategory(): ?CategoryInterface;

    public function setCategory(?CategoryInterface $category): void;
}
