<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig;

use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Owl\Component\Core\Manager\UserPreferenceManagerInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class InvoicesSummaryExtension extends AbstractExtension
{
    /**
     * @param RepositoryInterface<InvoiceInterface> $companyRepository
     */
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

    /**
     * @param Pagerfanta<InvoiceInterface> $invoices
     * @return array<string, float|string>
     */
    public function getAllCurrencies(string $gridName, Pagerfanta $invoices): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $company = $request->get('criteria')['company'] ?? $this->userPreferenceManager->get('filters.' . $gridName . '.company');

        if (empty($company)) {
            return [];
        }

        $company = $this->companyRepository->find($company);

        if (null === $company) {
            return [];
        }

        $sum = [
            'subtotal' => 0.0,
            'tax' => 0.0,
            'total' => 0.0,
            'currency' => $company->getCurrency()->getCode(),
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