<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
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

#[AsLiveComponent]
class InvoiceNumberingComponent
{
    use ComponentToolsTrait;
    use TemplatePropTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public const OWL_ADMIN_NUMBER_WITH_SERIE_CHANGED = 'owl:admin:number_with_serie_changed';

    /** @var array<string, mixed> */
    #[LiveProp(hydrateWith: 'hydrateSeries', dehydrateWith: 'dehydrateSeries')]
    public array $series = [];

    #[LiveProp(writable: true)]
    public string $issueDate = '';

    #[LiveProp(writable: true)]
    public string $fullNumberPreview = '';

    #[LiveProp(writable: true)]
    public bool $showPreview = false;

    #[LiveProp(writable: true)]
    public bool $showInputFullNumber = false;

    public function __construct(
        LiveResponder $liveReponser,
        private FormFactoryInterface $formFactory,
        private string $formClass,
        private readonly InvoiceNumberGeneratorInterface $invoiceNumberGenerator,
    ) {
        $this->setLiveResponder($liveReponser);
    }

    /** @return array<string, mixed> */
    public function hydrateSeries(string $series): array
    {
        return json_decode($series, true);
    }

    /** @param array<string, mixed>|null $series */
    public function dehydrateSeries(?array $series): string
    {
        return json_encode($series ?? []);
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
    public function confirm(): void
    {
        $this->submitForm(true);

        $formData = $this->getForm()->getData();

        if (empty($formData['serie'])) {
            $fullNumberPreview = $fullNumber = $formData['fullNumber'];
        } else {
            $fullNumber = '';
            $fullNumberPreview = $this->fullNumberPreview;
        }

        $this->emit(self::OWL_ADMIN_NUMBER_WITH_SERIE_CHANGED, [
            'sequenceNumber' => $formData['number'],
            'serie' => $formData['serie'],
            'fullNumber' => $fullNumber,
            'fullNumberPreview' => $fullNumberPreview,
        ]);
    }

    #[LiveAction]
    public function changeSerie(#[LiveArg] string $serieValue): void
    {
        $serie = $this->series[$serieValue] ?? null;

        if (!empty($serie)) {
            $this->formValues['serie'] = $serieValue;
            $this->formValues['number'] = $serie['nextCounter'];
            $this->fullNumberPreview = $serie['nextValue'];
        } else {
            $this->formValues['serie'] = '';
        }
    }
}
