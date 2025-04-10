<?php

declare(strict_types=1);

namespace Owl\Bundle\LocaleBundle\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class OwlContractorBundleTest extends WebTestCase
{
    /**
     * @test
     */
    public function its_services_are_initializable(): void
    {
        /** @var ContainerBuilder $container */
        $container = self::createClient()->getContainer();

        $services = $container->getServiceIds();

        $services = array_filter($services, function (string $serviceId): bool {
            return 0 === strpos($serviceId, 'owl.');
        });

        foreach ($services as $id) {
            Assert::assertNotNull($container->get($id, ContainerInterface::NULL_ON_INVALID_REFERENCE));
        }
    }
}
