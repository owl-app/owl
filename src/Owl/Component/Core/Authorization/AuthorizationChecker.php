<?php

declare(strict_types=1);

namespace Owl\Component\Core\Authorization;

use Owl\Bridge\SyliusResource\Controller\AuthorizationCheckerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as SymfonyAuthorizationCheckerInterface;

final class AuthorizationChecker implements AuthorizationCheckerInterface
{
    private SymfonyAuthorizationCheckerInterface $authorizationChecker;

    public function __construct(SymfonyAuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function isGranted(RequestConfiguration $configuration, $permission = null): bool
    {
        $route = $configuration->getRequest()->attributes->get('_route');

        return $this->authorizationChecker->isGranted($route, $permission);
    }
}
