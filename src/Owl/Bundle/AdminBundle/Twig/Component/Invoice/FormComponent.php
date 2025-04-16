<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

use ApiPlatform\GraphQl\Resolver\Stage\WriteStage;
use Owl\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Owl\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
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
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Symfony\UX\TwigComponent\Attribute\PreMount;

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

    /**
     * @param RepositoryInterface<InvoiceInterface> $invoiceRepository
     */
    public function __construct(
        RepositoryInterface $invoiceRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
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

    #[LiveAction]
    public function dateIssueChanged(#[LiveArg] string $oldDate): void
    {
        $this->submitForm(false);

        /** @var InvoiceInterface $invoice */
        $invoice = $this->getForm()->getData();
        /** @var InvoiceSerieInterface $serie */
        $serie = $this->getForm()->getData()?->getSerie();

        /** @var InvoiceSequenceStrategyInterface $strategy */
        $strategy = $this->registryInvoiceSequenceStrategy->get($serie->getSequenceIncrement());
        $invoiceSequence = $strategy->getNextCounter($serie, $invoice->getIssueDate());
        $nextCounter = $invoiceSequence->getNextCounter();

        $fullNumber = $this->invoiceNumberGenerator->generate($serie->getFormat(), $nextCounter, $invoice->getIssueDate());
        
        $this->formValues['sequenceNumber'] = $this->getFormView()
            ->offsetGet('sequenceNumber')->vars['value'] = $nextCounter;
        $this->fullNumberPreview = $fullNumber;
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
}
