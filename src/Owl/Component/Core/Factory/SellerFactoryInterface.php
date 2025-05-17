<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of SellerInterface
 *
 * @extends FactoryInterface<T>
 */
interface SellerFactoryInterface extends FactoryInterface
{
    public function createFromCompany(CompanyInterface $contractor): SellerInterface;
}
