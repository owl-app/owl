<?php

declare(strict_types=1);

namespace Owl\Component\Core\Factory;

use Owl\Component\Contractor\Model\ContractorInterface;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of BuyerInterface
 *
 * @implements BuyerFactoryInterface<T>
 */
final class BuyerFactory implements BuyerFactoryInterface
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
    public function createFromContractor(ContractorInterface $contractor): BuyerInterface
    {
        $buyer = $this->decoratedFactory->createNew();
        $buyer->setCompany($contractor->getCompanyName());
        $buyer->setTaxNumber($contractor->getTaxNumber());
        $buyer->setStreet($contractor->getStreet());
        $buyer->setCity($contractor->getCity());
        $buyer->setPostCode($contractor->getPostcode());

        return $buyer;
    }
}
