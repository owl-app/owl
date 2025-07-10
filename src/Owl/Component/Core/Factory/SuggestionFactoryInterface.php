<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\SuggestionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface SuggestionFactoryInterface extends FactoryInterface
{
    public function createAction(string $status, AdminUserInterface $owner): SuggestionInterface;
}
