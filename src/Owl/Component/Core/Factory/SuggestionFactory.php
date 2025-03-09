<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\SuggestionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of SuggestionInterface
 *
 * @implements SuggestionFactoryInterface<T>
 */
final class SuggestionFactory implements SuggestionFactoryInterface
{
    private FactoryInterface $defaultFactory;

    public function __construct(FactoryInterface $defaultFactory)
    {
        $this->defaultFactory = $defaultFactory;
    }

    public function createNew(): SuggestionInterface
    {
        return $this->defaultFactory->createNew();
    }

    public function createAction(string $status, AdminUserInterface $owner): SuggestionInterface
    {
        /** @var SuggestionInterface $suggestion */
        $suggestion = $this->defaultFactory->createNew();
        $suggestion->setStatus($status);
        $suggestion->setUser($owner);

        return $suggestion;
    }
}
