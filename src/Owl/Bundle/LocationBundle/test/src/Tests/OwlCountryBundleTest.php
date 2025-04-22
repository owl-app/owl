<?php



declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class php extends WebTestCase
{
    /**
     * @test
     */
    public function its_services_are_initializable(): void
    {
        /** @var ContainerBuilder $container */
        $container = self::createClient()->getContainer();

        $services = $container->getServiceIds();

        $services = array_filter($services, fn (string $serviceId): bool => str_starts_with($serviceId, 'sylius.'));

        foreach ($services as $id) {
            Assert::assertNotNull($container->get($id, ContainerInterface::NULL_ON_INVALID_REFERENCE));
        }
    }
}
