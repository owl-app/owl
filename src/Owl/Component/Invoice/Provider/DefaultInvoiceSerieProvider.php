<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Provider;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class DefaultInvoiceSerieProvider implements InvoiceSerieProviderInterface
{
    public function __construct(
        private readonly RepositoryInterface $serieRepository,
    ) {}

    public function getSerie(string $type): InvoiceSerieInterface
    {
        $default =  $this->serieRepository->findOneBy(['isDefault' => true, 'invoiceType' => $type]);

        if ($default) {
            return $default;
        }

        $first = $this->serieRepository->findBy(['invoiceType' => $type], ['id' => 'ASC'], 1);

        if (count($first) === 0) {
            throw new \RuntimeException('No invoice serie found');
        }

        return $first[0];
    }
}