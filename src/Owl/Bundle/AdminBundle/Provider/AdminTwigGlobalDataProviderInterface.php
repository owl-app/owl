<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Provider;

interface AdminTwigGlobalDataProviderInterface
{
    public function getNotifications(): array;
}
