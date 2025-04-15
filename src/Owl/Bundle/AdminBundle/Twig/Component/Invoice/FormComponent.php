<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

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
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;

#[AsLiveComponent]
class FormComponent
{
    public const ATTRIBUTE_REMOVED_EVENT = 'sylius_admin:product:form:attributed_deleted';

    public const AUTOCOMPLETE_CLEAR_REQUESTED_EVENT = 'sylius_admin.product_attribute_autocomplete.clear_requested';

    use ComponentToolsTrait;
    use LiveCollectionTrait;
    use TemplatePropTrait;

    /** @use ResourceFormComponentTrait<ProductInterface> */
    use ResourceFormComponentTrait;

    #[LiveProp(fieldName: 'type')]
    public string $type;

    #[LiveProp]
    public string $fullNumberPreview;

    /**
     * @param RepositoryInterface<InvoiceInterface> $invoiceRepository
     * @param RepositoryInterface<InvoiceSerieInterface> $serieRepository
     */
    public function __construct(
        RepositoryInterface $invoiceRepository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        private RepositoryInterface $serieRepository,
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

    #[LiveAction]
    public function dateIssueChanged(): void
    {
        $this->submitForm($this->isValidated);
        $this->shouldAutoSubmitForm = false;
        $serie = $this->serieRepository->find($this->formValues['serie']);

        if (empty($this->formValues['issueDate']) || empty($this->formValues['serie'])) {
            return;
        }

        $date = new \DateTime($this->formValues['issueDate']);

        /** @var InvoiceSequenceStrategyInterface $incrementStrategy */
        $strategy = $this->registryInvoiceSequenceStrategy->get($serie->getSequenceIncrement());
        $invoiceSequence = $strategy->getNextCounter($serie, $date);

        $fullNumber = $this->invoiceNumberGenerator->generate($serie, $invoiceSequence->getNextCounter(), $date);

        $this->formValues['sequenceNumber'] = $invoiceSequence->getNextCounter();
        $this->fullNumberPreview = $fullNumber;
    }
}
