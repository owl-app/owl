<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Suggestion\Model\SuggestionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of SuggestionInterface
 *
 * @extends RepositoryInterface<T>
 */
interface SuggestionRepositoryInterface extends RepositoryInterface
{
}
