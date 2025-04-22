<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Owl\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

final class MainMenuBuilder
{
    public const EVENT_NAME = 'owl.menu.admin.main';

    /** @var AuthorizationChecker */
    public $authorizationChecker;

    /** @var FactoryInterface */
    private $factory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(AuthorizationChecker $authorizationChecker, FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $this->addDashboardMenu($menu);
        $this->addSuggestionMenu($menu);
        $this->addNotificationMenu($menu);
        $this->addSaleMenu($menu);
        $this->addConfigurationSubMenu($menu);
        $this->addPermissionSubMenu($menu);

        $this->eventDispatcher->dispatch(new MenuBuilderEvent($this->factory, $menu), self::EVENT_NAME);

        return $menu;
    }

    public function addDashboardMenu(ItemInterface $menu): void
    {
        $menu
            ->addChild('dashboard', ['route' => 'owl_admin_dashboard'])
            ->setLabel('owl.menu.admin.main.dashboard.header')
            ->setLabelAttribute('icon', 'flowbite:home-outline');
    }

    private function addSuggestionMenu(ItemInterface $menu): void
    {
        $isGranted = $this->authorizationChecker->isGranted('owl_admin_suggestion_index');

        if ($isGranted) {
            $menu
                ->addChild('suggestion', ['route' => 'owl_admin_suggestion_index'])
                ->setLabel('owl.menu.admin.main.suggestion.header')
                ->setLabelAttribute('icon', 'flowbite:question-circle-outline');
        }
    }

    public function addNotificationMenu(ItemInterface $menu): void
    {
        $isGranted = $this->authorizationChecker->isGranted('owl_admin_notification_index');

        if ($isGranted) {
            $menu
                ->addChild('notification', [
                    'route' => 'owl_admin_notification_index',
                    'extras' => [
                        'routes' => [
                            ['route' => 'owl_admin_notification_update'],
                            ['route' => 'owl_admin_notification_show'],
                            ['route' => 'owl_admin_notification_show_accepted_index'],
                        ]
                    ]
                ])
                ->setLabel('owl.menu.admin.main.notification.header')
                ->setLabelAttribute('icon', 'flowbite:bullhorn-outline');
        }
    }

    public function addSaleMenu(ItemInterface $menu): void
    {
        $isGrantedInvoices = $this->authorizationChecker->isGranted('owl_admin_invoice_index');
        $isGrantedContractors = $this->authorizationChecker->isGranted('owl_admin_contractor_index');

        if ($isGrantedInvoices || $isGrantedContractors) {
            $configuration = $menu
                ->addChild('sale')
                ->setLabel('owl.menu.admin.main.sale.header')
                ->setLabelAttribute('icon', 'flowbite:cash-outline')
                ->setExtra('always_open', true);

            if ($isGrantedInvoices) {
                $configuration
                    ->addChild('admin_invoices', [
                        'route' => 'owl_admin_invoice_index',
                        'extras' => [
                            'routes' => [
                                ['route' => 'owl_admin_invoice_create'],
                                ['route' => 'owl_admin_invoice_update'],
                            ]
                        ]
                    ])
                    ->setLabel('owl.menu.admin.main.sale.invoices');
            }

            if ($isGrantedContractors) {
                $configuration
                    ->addChild('admin_contractors', [
                        'route' => 'owl_admin_contractor_index',
                        'extras' => [
                            'routes' => [
                                ['route' => 'owl_admin_contractor_create'],
                                ['route' => 'owl_admin_contractor_update'],
                            ]
                        ]
                    ])
                    ->setLabel('owl.menu.admin.main.sale.contractors');
            }
        }
    }

    private function addConfigurationSubMenu(ItemInterface $menu): void
    {
        $isGrantedLocales = $this->authorizationChecker->isGranted('owl_admin_locale_index');
        $isGrantedUsers = $this->authorizationChecker->isGranted('owl_admin_admin_user_index');
        $isGrantedSettings = $this->authorizationChecker->isGranted('owl_admin_setting_index');
        $isGrantedInvoiceNumberFormat = $this->authorizationChecker->isGranted('owl_admin_invoice_serie_index');
        $isGrantedInvoiceTaxRate = $this->authorizationChecker->isGranted('owl_admin_invoice_tax_rate_index');
        $isGrantedInvoiceCountries = $this->authorizationChecker->isGranted('owl_admin_country_index');
        $isGrantedInvoiceZones= $this->authorizationChecker->isGranted('owl_admin_zone_index');

        if ($isGrantedLocales || $isGrantedUsers || $isGrantedSettings || $isGrantedInvoiceNumberFormat) {
            $configuration = $menu
                ->addChild('configuration')
                ->setLabel('owl.menu.admin.main.configuration.header')
                ->setLabelAttribute('icon', 'flowbite:cog-outline')
                ->setExtra('always_open', true);

            if ($isGrantedInvoiceNumberFormat) {
                $configuration
                    ->addChild('admin_invoice_serie', [
                        'route' => 'owl_admin_invoice_serie_index',
                    ])
                    ->setLabel('owl.menu.admin.main.configuration.invoice_series')
                ;
            }

            if ($isGrantedInvoiceTaxRate) {
                $configuration
                    ->addChild('admin_invoice_tax_rate', [
                        'route' => 'owl_admin_invoice_tax_rate_index',
                    ])
                    ->setLabel('owl.menu.admin.main.configuration.invoice_tax_rates')
                ;
            }

            if ($isGrantedInvoiceCountries) {
                $configuration
                    ->addChild('admin_countries', [
                        'route' => 'owl_admin_country_index',
                    ])
                    ->setLabel('owl.menu.admin.main.configuration.countries')
                ;
            }

            if ($isGrantedInvoiceZones) {
                $configuration
                    ->addChild('admin_zones', [
                        'route' => 'owl_admin_zone_index',
                    ])
                    ->setLabel('owl.menu.admin.main.configuration.zones')
                ;
            }

            if ($isGrantedLocales) {
                $configuration
                    ->addChild('admin_locales', ['route' => 'owl_admin_locale_index'])
                    ->setLabel('owl.menu.admin.main.configuration.locales')
                ;
            }

            if ($isGrantedUsers) {
                $configuration
                    ->addChild('admin_users', [
                        'route' => 'owl_admin_admin_user_index',
                        'extras' => [
                            'routes' => [
                                ['route' => 'owl_admin_admin_user_create'],
                                ['route' => 'owl_admin_admin_user_update'],
                                ['route' => 'owl_admin_admin_user_permissions'],
                            ]
                        ]
                    ])
                    ->setLabel('owl.menu.admin.main.configuration.users')
                ;
            }

            if ($isGrantedSettings) {
                $configuration
                    ->addChild('admin_settings', ['route' => 'owl_admin_setting_index'])
                    ->setLabel('owl.menu.admin.main.configuration.settings')
                ;
            }
        }
    }

    private function addPermissionSubMenu(ItemInterface $menu): void
    {
        $isGrantedPermissionRoles = $this->authorizationChecker->isGranted('owl_admin_rbac_role_index');
        $isGrantedPermissionAvailableRoutes = $this->authorizationChecker->isGranted('owl_admin_rbac_permission_availables');

        if ($isGrantedPermissionRoles || $isGrantedPermissionAvailableRoutes) {
            $configuration = $menu
                ->addChild('permission')
                ->setLabel('owl.menu.admin.main.permission.header')
                ->setLabelAttribute('icon', 'flowbite:lock-outline')
                ->setExtra('always_open', true);

            if ($isGrantedPermissionRoles) {
                $configuration
                    ->addChild('admin_permission_roles', [
                        'route' => 'owl_admin_rbac_role_index',
                        'extras' => [
                            'routes' => [
                                ['route' => 'owl_admin_rbac_role_permissions']
                            ]
                        ]
                    ])
                    ->setLabel('owl.menu.admin.main.permission.roles')
                    ->setLabelAttribute('icon', 'user secret')
                    ->setExtra('is_granted', $isGrantedPermissionRoles);
            }

            if ($isGrantedPermissionAvailableRoutes) {
                $configuration
                    ->addChild('admin_permission_available_routes', ['route' => 'owl_admin_rbac_permission_availables'])
                    ->setLabel('owl.menu.admin.main.permission.available')
                    ->setLabelAttribute('icon', 'lock')
                    ->setExtra('is_granted', $isGrantedPermissionAvailableRoutes);
            }
        }
    }
}
