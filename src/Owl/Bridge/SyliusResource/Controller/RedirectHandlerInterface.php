<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Controller;

use Sylius\Resource\Model\ResourceInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface as SyliusRedirectHandlerInterface;

interface RedirectHandlerInterface extends SyliusRedirectHandlerInterface
{
    /**
     * @return array<string, string>
     */
    public function getRedirectHeaders(RequestConfiguration $configuration, ?ResourceInterface $resource): array;
}