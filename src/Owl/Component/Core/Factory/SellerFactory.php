<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of SellerInterface
 *
 * @implements SellerFactoryInterface<T>
 */
final class SellerFactory implements SellerFactoryInterface
{
    public function __construct(
        private FactoryInterface $decoratedFactory,
    ) {
    }

    /**
     * @throws UnsupportedMethodException
     */
    public function createNew(): object
    {
        throw new UnsupportedMethodException('createNew');
    }

    /** @inheritdoc */
    public function createFromCompany(CompanyInterface $company): SellerInterface
    {
        /** @var SellerInterface $seller */
        $seller = $this->decoratedFactory->createNew();
        $seller->setCompany($company->getName());
        $seller->setTaxNumber($company->getTaxNumber());
        $seller->setStreet($company->getStreet());
        $seller->setCity($company->getCity());
        $seller->setPostCode($company->getPostcode());

        return $seller;
    }
}
