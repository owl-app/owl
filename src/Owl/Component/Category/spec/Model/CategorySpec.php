<?php

declare(strict_types=1);

namespace spec\Owl\Component\Category\Model;

use Owl\Component\Category\Model\CategoryInterface;
use PhpSpec\ObjectBehavior;

final class CategorySpec extends ObjectBehavior
{
    function it_implements_category_interface(): void
    {
        $this->shouldImplement(CategoryInterface::class);
    }

    function it_has_a_name(): void
    {
        $this->setName('category name');
        $this->getName()->shouldReturn('category name');
    }

    function it_has_a_created_at(\DateTime $createdAt): void
    {
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function it_has_an_updated_at(\DateTime $updatedAt): void
    {
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }
}
