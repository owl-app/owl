<?php



declare(strict_types=1);

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new BabDev\PagerfantaBundle\BabDevPagerfantaBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Owl\Bundle\LocationBundle\OwlCountryBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle(),
            new Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }
}
