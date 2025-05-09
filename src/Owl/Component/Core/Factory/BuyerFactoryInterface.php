<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Contractor\Model\ContractorInterface;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of BuyerInterface
 *
 * @extends FactoryInterface<T>
 */
interface BuyerFactoryInterface extends FactoryInterface
{
    public function createFromContractor(ContractorInterface $contractor): BuyerInterface;
}
