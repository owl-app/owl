<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Shared\Navbar;

use Owl\Bundle\AdminBundle\Provider\LoggedInAdminUserProviderInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class UserDropdownComponent
{
    public function __construct(
        protected readonly UrlGeneratorInterface $urlGenerator,
        protected readonly LoggedInAdminUserProviderInterface $loggedInAdminUserProvider,
    ) {
    }

    #[ExposeInTemplate(name: 'user')]
    public function getUser(): AdminUserInterface
    {
        $user = $this->loggedInAdminUserProvider->getUser();
        if (!$user instanceof AdminUserInterface) {
            throw new \RuntimeException('User must be an instance of ' . AdminUserInterface::class . '.');
        }

        return $user;
    }

    /**
     * @return array<array-key, array{title?: string, url?: string, icon?: string, type?: string, class?: string}>
     */
    #[ExposeInTemplate(name: 'menu_items')]
    public function getMenuItems(): array
    {
        // TODO: Would be nice to have these set via hook //
        return [
            [
                'title' => 'owl.ui.my_account',
                'url' => $this->urlGenerator->generate('owl_admin_profile_update', ['id' => $this->getUser()->getId()]),
                'icon' => 'flowbite:user-outline',
            ],
            [
                'title' => 'owl.ui.logout',
                'url' => $this->urlGenerator->generate('owl_admin_logout'),
                'icon' => 'flowbite:arrow-left-to-bracket-outline',
            ],
        ];
    }
}
