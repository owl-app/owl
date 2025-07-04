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

namespace Sylius\Bundle\CoreBundle\Fixture\OptionsResolver;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Exception\ResourceNotFoundException;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Webmozart\Assert\Assert;

/**
 * Using the hacky hack to distinct between option which wasn't set
 * and option which was set to empty.
 *
 * Usage:
 *
 *   $optionsResolver
 *     ->setDefault('option', LazyOption::randomOne($repository))
 *     ->setNormalizer('option', LazyOption::findOneBy($repository, 'code'))
 *   ;
 *
 *   Returns:
 *     - null if user explicitly set it (['option' => null])
 *     - random one if user skipped that option ([])
 *     - specific one if user defined that option (['option' => 'CODE'])
 */
final class LazyOption
{
    /**
     * @param array<string, mixed> $criteria
     *
     * @return \Closure(Options):object
     */
    public static function randomOne(RepositoryInterface $repository, array $criteria = []): \Closure
    {
        return function (Options $options) use ($repository, $criteria): object {
            /** @var array<array-key, object>|Collection<array-key, object> $objects */
            $objects = $repository->findBy($criteria);

            if ($objects instanceof Collection) {
                $objects = $objects->toArray();
            }

            Assert::notEmpty($objects, 'No entities found of type ' . $repository->getClassName());

            return $objects[array_rand($objects)];
        };
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return \Closure(Options):(object|null)
     */
    public static function randomOneOrNull(
        RepositoryInterface $repository,
        int $chanceOfRandomOne = 100,
        array $criteria = []
    ): \Closure {
        return function (Options $options) use ($repository, $chanceOfRandomOne, $criteria): ?object {
            if (random_int(1, 100) > $chanceOfRandomOne) {
                return null;
            }

            /** @var array<array-key, object>|Collection<array-key, object> $objects */
            $objects = $repository->findBy($criteria);

            if ($objects instanceof Collection) {
                $objects = $objects->toArray();
            }

            return 0 === count($objects) ? null : $objects[array_rand($objects)];
        };
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return \Closure(Options):list<ResourceInterface>
     */
    public static function randomOnes(RepositoryInterface $repository, int $amount, array $criteria = []): \Closure
    {
        return function (Options $options) use ($repository, $amount, $criteria): array {
            /** @var array<array-key, ResourceInterface>|Collection<array-key, ResourceInterface> $objects */
            $objects = $repository->findBy($criteria);

            if ($objects instanceof Collection) {
                $objects = $objects->toArray();
            }

            /** @var list<ResourceInterface> $selectedObjects */
            $selectedObjects = [];
            for (; $amount > 0 && count($objects) > 0; --$amount) {
                $randomKey = array_rand($objects);

                $selectedObjects[] = $objects[$randomKey];

                unset($objects[$randomKey]);
            }

            return $selectedObjects;
        };
    }

    /**
     * @return \Closure(Options):array<ResourceInterface>
     */
    public static function all(RepositoryInterface $repository): \Closure
    {
        return function (Options $options) use ($repository): array {
            /** @var array<ResourceInterface> $result */
            $result = $repository->findAll();

            return $result;
        };
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return \Closure(Options, array<array-key, mixed>|null):array<array-key, object|null>|null
     */
    public static function findBy(RepositoryInterface $repository, string $field, array $criteria = []): \Closure
    {
        return function (Options $options, ?array $previousValues) use ($repository, $field, $criteria): ?array {
            if (null === $previousValues || [] === $previousValues) {
                return $previousValues;
            }

            /** @var array<array-key, object|null> $resources */
            $resources = [];
            foreach ($previousValues as $previousValue) {
                if (is_object($previousValue)) {
                    $resources[] = $previousValue;
                } else {
                    $resources[] = $repository->findOneBy(array_merge($criteria, [$field => $previousValue]));
                }
            }

            return $resources;
        };
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return \Closure(Options, mixed):(object|null)
     */
    public static function findOneBy(RepositoryInterface $repository, string $field, array $criteria = []): \Closure
    {
        return function (Options $options, $previousValue) use ($repository, $field, $criteria): ?object {
            if (null === $previousValue || [] === $previousValue) {
                return null;
            }

            if (is_object($previousValue)) {
                return $previousValue;
            }

            return $repository->findOneBy(array_merge($criteria, [$field => $previousValue]));
        };
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return \Closure(Options, mixed):(object|null)
     */
    public static function getOneBy(RepositoryInterface $repository, string $field, array $criteria = []): \Closure
    {
        return function (Options $options, $previousValue) use ($repository, $field, $criteria): ?object {
            if (null === $previousValue || [] === $previousValue) {
                return null;
            }

            if (is_object($previousValue)) {
                return $previousValue;
            }

            $resource = $repository->findOneBy(array_merge($criteria, [$field => $previousValue]));

            if (null === $resource) {
                throw new ResourceNotFoundException(
                    sprintf(
                        'The %s resource for field %s with value %s was not found',
                        $repository->getClassName(),
                        $field,
                        (string) $previousValue
                    )
                );
            }

            return $resource;
        };
    }
}