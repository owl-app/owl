<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

use Owl\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Owl\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Core\Model\ContractorInterface;
use Owl\Component\Core\Model\CompanyInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Calculator\LineDataCalculator;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
class FormComponent
{
    use ComponentToolsTrait;
    use LiveCollectionTrait;
    use TemplatePropTrait;
    /** @use ResourceFormComponentTrait<ProductInterface> */
    use ResourceFormComponentTrait;

    #[LiveProp]
    public string $type;

    #[LiveProp(writable: true)]
    public string $fullNumberPreview;

    #[LiveProp(writable: true)]
    public bool $showPaymentDate = false;

    #[LiveProp(writable: true)]
    public string $exchangeRateCurrency = '';

    /**
     * @param RepositoryInterface<InvoiceInterface> $invoiceRepository
     * @param RepositoryInterface<CompanyInterface> $companyRepository
     * @param RepositoryInterface<ContractorInterface> $contractorRepository
     */
    public function __construct(
        RepositoryInterface $invoiceRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        private ExchangeRateCurrencyResolverInterface $exchangeRateCurrencyResolver,
        private RepositoryInterface $companyRepository,
        private RepositoryInterface $contractorRepository,
        private readonly InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
        private ServiceRegistryInterface $registryInvoiceSequenceStrategy,
    ) {
        $this->initialize($invoiceRepository, $formFactory, $resourceClass, $formClass);
    }

    protected function instantiateForm(): FormInterface
    {
        $this->resource->setType($this->type);

        return $this->formFactory->create($this->formClass, $this->resource);
    }

    #[PostMount]
    public function initializePreview(): void
    {
        /** @var InvoiceInterface $resource */
        $resource = $this->resource;

        $this->fullNumberPreview = $this->invoiceNumberGenerator->generate(
            $resource->getSerie()?->getFormat(),
            $resource->getSequenceNumber(),
            $resource->getIssueDate()
        );
    }

    #[PreReRender(priority: 100)]
    public function defaultValuesLineItems(): void
    {
        if (!isset($this->formValues['lineItems'])) {
            return;
        }

        foreach ($this->formValues['lineItems'] as $key => $lineItem) {
            if (!isset($lineItem['unitPrice'])) {
                $this->formValues['lineItems'][$key]['unitPrice'] = 0;
            }

            if (!isset($lineItem['totalPrice'])) {
                $this->formValues['lineItems'][$key]['totalPrice'] = 0;
            }

            if (!isset($lineItem['quantity'])) {
                $this->formValues['lineItems'][$key]['quantity'] = 1;
            }

            if (!isset($lineItem['unit'])) {
                $this->formValues['lineItems'][$key]['unit'] = LineItemInterface::UNIT_PIECE;
            }
        }
    }

    #[PreReRender(priority: -100)]
    public function toggleShowPaymentDate(): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->getForm()->getData();
        
        if ($invoice->isPaid()) {
            $this->showPaymentDate = true;
        } else {
            $this->showPaymentDate = false;
        }
    }

    #[LiveAction]
    public function changeCompany(): void
    {
        try {
            $this->submitForm(false);
        } catch (\Throwable $e) {
            // do nothing
        }

        /** @var InvoiceInterface $invoice */
        $invoice = $this->getForm()->getData();

        if ($company = $invoice->getCompany()) {

            if ($company) {
                $invoice->setCurrency($company->getCurrency());
                $this->formValues['currency'] = $this->getFormView()
                    ->offsetGet('currency')
                    ->vars['value'] = $company->getCurrency()?->getCode();
            }

            $this->setExchangeRateCurrency($invoice);
        }
    }

    #[LiveAction]
    public function changeExchangeRateCurrency(): void
    {
        try {
            $this->submitForm(false);
        } catch (\Throwable $e) {
            // do nothing
        }

        /** @var InvoiceInterface $invoice */
        $invoice = $this->getForm()->getData();

        $this->setExchangeRateCurrency($invoice);
    }

    #[LiveAction]
    public function dateIssueChanged(#[LiveArg] string $oldDate): void
    {
        try {
            $this->submitForm(false);
        } catch (\Throwable $e) {
            // do nothing
        }

        /** @var InvoiceInterface $invoice */
        $invoice = $this->getForm()->getData();
        /** @var InvoiceSerieInterface $serie */
        $serie = $invoice?->getSerie();

        if(null === $serie) {
            return;
        }

        /** @var InvoiceSequenceStrategyInterface $strategy */
        $strategy = $this->registryInvoiceSequenceStrategy->get($serie->getSequenceIncrement());
        $invoiceSequence = $strategy->getNextCounter($serie, $invoice->getIssueDate());
        $nextCounter = $invoiceSequence->getNextCounter();
        $fullNumber = $this->invoiceNumberGenerator->generate($serie->getFormat(), $nextCounter, $invoice->getIssueDate());
        
        $this->formValues['sequenceNumber'] = $this->getFormView()
            ->offsetGet('sequenceNumber')
            ->vars['value'] = $nextCounter
        ;
        $this->fullNumberPreview = $fullNumber;
    }

    #[LiveAction]
    public function quantityChanged(#[LiveArg] string $key, #[LiveArg] string $value): void
    {
        if ($this->tryCalculateByUnitPrice($key, null, $value)) {
            return;
        }

        $this->tryCalculateBySum($key, null, $value);
    }

    #[LiveAction]
    public function unitPriceChanged(#[LiveArg] string $key, #[LiveArg] string $value): void
    {
        $this->tryCalculateByUnitPrice($key, $value);
    }

    #[LiveAction]
    public function sumChanged(#[LiveArg] string $key, #[LiveArg] string $value): void
    {
        $this->tryCalculateBySum($key, $value);
    }

    #[LiveListener(InvoiceNumberingComponent::OWL_ADMIN_NUMBER_WITH_SERIE_CHANGED)]
    public function numberWithSerieChanged(
        #[LiveArg] string $sequenceNumber,
        #[LiveArg] string $serie,
        #[LiveArg] ?string $fullNumber,
        #[LiveArg] string $fullNumberPreview
    ): void
    {
        $this->formValues['sequenceNumber'] = $sequenceNumber;
        $this->formValues['serie'] = $serie;
        $this->formValues['fullNumber'] = $fullNumber;
        $this->fullNumberPreview = $fullNumberPreview;
    }

    private function tryCalculateBySum(string $key, ?string $totalPrice = null, ?string $quantity = null): bool
    {
        $totalPrice = (float) ($totalPrice ?? $this->formValues['lineItems'][$key]['totalPrice']);
        $quantity = (float) ($quantity ?? $this->formValues['lineItems'][$key]['quantity']);

        $result = LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor($totalPrice, $quantity);

        if ($result !== null) {
            list($unitPriceCalculated, $totalPriceCalculated) = $result;

            $this->formValues['lineItems'][$key]['unitPrice'] = $unitPriceCalculated;

            if ($totalPriceCalculated !== null) {
                $this->formValues['lineItems'][$key]['totalPrice'] = $totalPriceCalculated;
            }

            return true;
        }

        return false;
    }

    private function tryCalculateByUnitPrice(string $key, ?string $unitPrice = null, ?string $quantity = null): bool
    {
        $unitPrice = (float) ($unitPrice ?? $this->formValues['lineItems'][$key]['unitPrice']);
        $quantity = (float) ($quantity ?? $this->formValues['lineItems'][$key]['quantity']);

        $totalPricePrice =  LineDataCalculator::calculateTotalPriceFromMajor($unitPrice, $quantity);

        if ($totalPricePrice > 0) {
            $this->formValues['lineItems'][$key]['totalPrice'] = $totalPricePrice;

            return true;
        }

        return false;
    }

    private function setExchangeRateCurrency(InvoiceInterface $invoice): void
    {
        $this->exchangeRateCurrency = $this->exchangeRateCurrencyResolver->resolve($invoice)?->getCode() ?? '';
    }
}
