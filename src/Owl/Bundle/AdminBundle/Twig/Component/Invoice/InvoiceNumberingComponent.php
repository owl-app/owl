<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

use ApiPlatform\GraphQl\Resolver\Stage\WriteStage;
use Owl\Bundle\AdminBundle\Form\Type\Invoice\InvoiceNumberingType;
use Owl\Bundle\UiBundle\Twig\Component\LiveCollectionTrait;
use Owl\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveResponder;
use Symfony\UX\TwigComponent\Attribute\PostMount;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent]
class InvoiceNumberingComponent
{
    use ComponentToolsTrait;
    use TemplatePropTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public const OWL_ADMIN_NUMBER_WITH_SERIE_CHANGED = 'owl:admin:number_with_serie_changed';

    #[LiveProp(hydrateWith: 'hydrateSeries', dehydrateWith: 'dehydrateSeries')]
    public array $series = [];

    #[LiveProp(writable: true)]
    public string $issueDate = '';

    #[LiveProp(writable: true)]
    public string $selectedSerie = '';

    #[LiveProp(writable: true)]
    public string $fullNumberPreview = '';

    #[LiveProp(writable: true)]
    public bool $showPreview = false;

    #[LiveProp(writable: true)]
    public bool $showInputFullNumber = false;
    

    /**
     * @param RepositoryInterface<InvoiceInterface> $invoiceRepository
     */
    public function __construct(
        LiveResponder $liveReponser,
        private FormFactoryInterface $formFactory,
        private string $formClass,
        private readonly InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
    ) {
        $this->setLiveResponder($liveReponser);
    }

    public function hydrateSeries(string $series): array
    {
        return json_decode($series, true);
    }

    public function dehydrateSeries(array|null $series): string
    {
        return json_encode($series);
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create($this->formClass, [], ['series' => $this->series]);
    }

    #[PreReRender(priority: -100)]
    public function preReRender(): void
    {
        $formData = $this->getForm()->getData();
        $date = new \DateTime($this->issueDate);
        $format = $this->series[$formData['serie']]['format'] ?? null;

        if ($format) {
            $this->fullNumberPreview = $this->invoiceNumberGenerator->generate($format, (int) $formData['number'], $date);

            $this->showPreview = true;
            $this->showInputFullNumber = false;
        } else {
            $this->showPreview = false;
            $this->showInputFullNumber = true;
        }
    }

    #[LiveAction]
    public function confirm()
    {
        $this->submitForm(true);

        $formData = $this->getForm()->getData();

        $this->emit(self::OWL_ADMIN_NUMBER_WITH_SERIE_CHANGED, [
            'sequenceNumber' => $formData['number'],
            'serie' => $formData['serie'],
            'fullNumber' => $formData['fullNumber'],
            'fullNumberPreview' => $this->fullNumberPreview,
        ]);
    }
}
