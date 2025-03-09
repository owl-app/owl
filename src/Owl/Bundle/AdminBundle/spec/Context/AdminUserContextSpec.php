<?php

declare(strict_types=1);

namespace spec\Owl\Bundle\AdminBundle\Context;

use Owl\Bundle\AdminBundle\Context\AdminUserContext;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Core\Model\Rbac\RoleSettingInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Owl\Component\User\Model\UserInterface;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;

class AdminUserContextSpec extends ObjectBehavior
{
    function let(TokenStorageInterface $tokenStorage)
    {
        $this->beConstructedWith($tokenStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AdminUserContext::class);
        $this->shouldImplement(AdminUserContextInterface::class);
    }

    function it_throws_exception_when_there_is_no_token(TokenStorageInterface $tokenStorage)
    {
        $tokenStorage->getToken()->willReturn(null);

        $this->shouldThrow(AuthenticationCredentialsNotFoundException::class)->during('getUser');
    }

    function it_returns_currently_logged_admin_user(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->getUser()->shouldReturn($user);
    }

    function it_returns_null_when_the_user_taken_from_token_is_not_an_admin(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $this->getUser()->shouldReturn(null);
    }

    function it_returns_null_when_there_is_no_user_in_the_token(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn(null);

        $this->getUser()->shouldReturn(null);
    }

    function it_returns_role_canonical_name_when_role_exists(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
        RoleSettingInterface $roleSetting,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getCanonicalName()->willReturn(RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME);

        $this->getRoleCanonicalName()->shouldReturn(RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME);
    }

    function it_returns_null_role_canonical_name_when_role_does_not_exist(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn(null);

        $this->getRoleCanonicalName()->shouldReturn(null);
    }

    function it_returns_theme_when_role_setting_exists(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
        RoleSettingInterface $roleSetting,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getSetting()->willReturn($roleSetting);
        $roleSetting->getTheme()->willReturn('owl/admin');

        $this->getTheme()->shouldReturn('owl/admin');
    }

    function it_returns_null_role_theme_when_role_does_not_exist(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn(null);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_null_role_theme_when_role_setting_does_not_exist(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getSetting()->willReturn(null);

        $this->getTheme()->shouldReturn(null);
    }

    function it_returns_true_when_role_canonical_name_does_not_match_admin_system(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
        RoleSettingInterface $roleSetting,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getCanonicalName()->willReturn(RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME);

        $this->isAdminSystem()->shouldReturn(true);
    }

    function it_returns_false_when_role_canonical_name_does_not_match_admin_system(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
        RoleSettingInterface $roleSetting,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getCanonicalName()->willReturn('ROLE_SYSTEM');

        $this->isAdminSystem()->shouldReturn(false);
    }

    function it_returns_true_when_role_canonical_name_matches_user(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
        RoleSettingInterface $roleSetting,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getCanonicalName()->willReturn(RoleAwareInterface::ROLE_USER_NAME);

        $this->isUser()->shouldReturn(true);
    }

    function it_returns_false_when_role_canonical_name_does_not_match_user(
        TokenStorageInterface $tokenStorage,
        TokenInterface $token,
        AdminUserInterface $user,
        RoleInterface $role,
        RoleSettingInterface $roleSetting,
    ) {
        $tokenStorage->getToken()->willReturn($token);
        $token->getUser()->willReturn($user);
        $user->getRole()->willReturn($role);
        $role->getCanonicalName()->willReturn('USER');

        $this->isUser()->shouldReturn(false);
    }
}
