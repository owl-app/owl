<?php



declare(strict_types=1);

namespace Owl\Bundle\LocationBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Owl\Bundle\LocationBundle\DependencyInjection\SyliusCountryExtension;

final class OwlCountryExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new SyliusCountryExtension(),
        ];
    }
}
