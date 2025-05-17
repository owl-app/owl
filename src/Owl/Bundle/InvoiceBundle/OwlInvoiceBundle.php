<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle;

use Owl\Bundle\InvoiceBundle\DependencyInjection\Compiler\RegisterSequentionStrategyPass;
use Owl\Bundle\InvoiceBundle\DependencyInjection\Compiler\RegisterSnapshotAssignersPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class OwlInvoiceBundle extends AbstractResourceBundle
{
    /**
     * @return list{'doctrine/orm'}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterSequentionStrategyPass());
        $container->addCompilerPass(new RegisterSnapshotAssignersPass());
    }

    /**
     * @return 'Owl\Component\Invoice\Model'
     */
    protected function getModelNamespace(): string
    {
        return 'Owl\Component\Invoice\Model';
    }
}
