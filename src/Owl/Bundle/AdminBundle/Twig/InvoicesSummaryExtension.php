<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig;

use Pagerfanta\Pagerfanta;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Owl\Component\Core\Manager\UserPreferenceManagerInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;

final class InvoicesSummaryExtension extends AbstractExtension
{
    public function __construct(
        private RepositoryInterface $companyRepository,
        private UserPreferenceManagerInterface $userPreferenceManager,
        private RequestStack $requestStack,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('owl_invoices_summary', [$this, 'getAllCurrencies']),
        ];
    }

    public function getAllCurrencies(string $gridName, Pagerfanta $invoices): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $company = $request->get('criteria')['company'] ?? $this->userPreferenceManager->get('filters.' . $gridName . '.company');

        if (empty($company)) {
            return [];
        }

        $company = $this->companyRepository->find($company);

        $sum = [
            'subtotal' => 0,
            'tax' => 0,
            'total' => 0,
            'currency' => $company->getCurrency()->getCode()
        ];

        /** @var InvoiceInterface $invoice */
        foreach ($invoices as $invoice) {
            $companyCurrencyCode = $company->getCurrency()->getCode();

            if (
                $companyCurrencyCode !== $invoice->getCurrency()->getCode() && 
                $companyCurrencyCode === $invoice->getExchangeRateSnapshot()->getCode()
            ) {
                $sum['subtotal'] += $invoice->getSubtotalConverted();
                $sum['tax'] += $invoice->getTaxTotalConverted();
                $sum['total'] += $invoice->getTotalConverted();
            } else {
                $sum['subtotal'] += $invoice->getSubtotal();
                $sum['tax'] += $invoice->getTaxTotal();
                $sum['total'] += $invoice->getTotal();
            }
        }

        return $sum;
    }
}
