<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Status\Model\StatusInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of StatusInterface
 *
 * @extends RepositoryInterface<T>
 */
interface SuggestionStatusRepositoryInterface extends RepositoryInterface
{
}
