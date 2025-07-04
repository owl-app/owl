<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use Owl\Component\Locale\Model\LocaleInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class LocaleFixture extends AbstractFixture
{
    public function __construct(private FactoryInterface $localeFactory, private ObjectManager $localeManager, private string $baseLocaleCode)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function load(array $options): void
    {
        /** @var array<string> $localesCodes */
        $localesCodes = $options['locales'];

        if ($options['load_default_locale']) {
            array_unshift($localesCodes, $this->baseLocaleCode);
        }

        $localesCodes = array_unique($localesCodes);

        foreach ($localesCodes as $localeCode) {
            /** @var LocaleInterface $locale */
            $locale = $this->localeFactory->createNew();

            $locale->setCode($localeCode);

            $this->localeManager->persist($locale);
        }

        $this->localeManager->flush();
    }

    public function getName(): string
    {
        return 'locale';
    }

    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->booleanNode('load_default_locale')->defaultTrue()->end()
                ->arrayNode('locales')->scalarPrototype()->end()
        ;
    }
}