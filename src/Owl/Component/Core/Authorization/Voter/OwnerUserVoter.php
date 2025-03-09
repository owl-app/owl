<?php

declare(strict_types=1);

namespace Owl\Component\Core\Authorization\Voter;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\Authorization\OwnerableUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template TAttribute of string
 * @template TSubject of mixed
 *
 * @extends Voter<TAttribute, TSubject>
 */
final class OwnerUserVoter extends Voter
{
    private AdminUserContextInterface $adminUserContext;

    public function __construct(AdminUserContextInterface $adminUserContext)
    {
        $this->adminUserContext = $adminUserContext;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if ($subject instanceof OwnerableUserInterface && $this->adminUserContext->isUser()) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        if ($subject->getUser()->getId() === $this->adminUserContext->getUser()->getId()) {
            return true;
        }

        return false;
    }
}
