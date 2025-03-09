<?php

declare(strict_types=1);

namespace Owl\Component\Core\Authorization\Voter;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template TAttribute of string
 * @template TSubject of mixed
 *
 * @extends Voter<TAttribute, TSubject>
 */
class RbacVoter extends Voter
{
    public function __construct(
        private RouterInterface $router,
        private AdminUserContextInterface $adminUserContext,
    ) {
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!empty($attribute) && null !== $this->router->getRouteCollection()->get($attribute)) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $this->adminUserContext->getUser();

        if ($user && in_array($attribute, $user->getPermissions())) {
            return true;
        }

        return false;
    }
}
