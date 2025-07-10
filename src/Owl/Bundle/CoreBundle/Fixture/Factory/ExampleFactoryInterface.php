<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Fixture\Factory;

interface ExampleFactoryInterface
{
    /**
     * @param array<string, mixed> $options
     *
     * @return object
     */
    public function create(array $options = []);
}
