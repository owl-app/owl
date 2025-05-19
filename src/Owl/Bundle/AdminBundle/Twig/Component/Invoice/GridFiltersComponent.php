<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Twig\Component\Invoice;

use Owl\Bundle\CoreBundle\Form\Type\Grid\Filter\PeriodFilterType;
use Owl\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
class GridFiltersComponent
{
    use ComponentToolsTrait;
    use TemplatePropTrait;
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(writable: true, url: true)]
    public string $query = '';

    /**
     * @param RepositoryInterface<InvoiceInterface> $invoiceRepository
     */
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        $form = $this->formFactory->createNamed('criteria', FormType::class, []);

        $form->add('period', PeriodFilterType::class, [
        ]);

        return $form;
    }

    #[PreReRender(priority: 100)]
    public function defaultValuesLineItems(): void
    {
        $this->query = 'test';
    }
}
