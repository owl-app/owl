<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\EventSubscriber;

use Owl\Bundle\InvoiceBundle\Form\Type\Taxation\TaxRateChoiceType;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class TaxRateSnapshotSubscriber implements EventSubscriberInterface
{
    public function __construct(

    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::POST_SET_DATA => 'postSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    public function postSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        /** @var LineItemInterface $lineItem */
        $lineItem = $event->getData();

        if ($lineItem === null) {
            return;
        }

        if ($lineItem->isTaxRateNameDiffrent()) {
            $form->add('taxRateSnapshotNameOverwrite', CheckboxType::class, [
                'label' => 'owl.ui.invoice.line_item.tax_rate_name_changed_overwrite',
                'mapped' => false,
                'required' => false,
            ]);

            $oldTaxRate = $form->get('taxRate')->getConfig();

            $form->add(
                $oldTaxRate->getName(),
                $oldTaxRate->getType()->getInnerType()::class,
                array_replace(
                    $oldTaxRate->getOptions(), 
                    [
                        'choice_label' => function (TaxRateInterface $taxRate) use ($lineItem) {
                            if ($taxRate->getCode() === $lineItem->getTaxRateSnapshot()->getCode() && $taxRate->getName() !== $lineItem->getTaxRateSnapshot()->getName()) {
                                return $lineItem->getTaxRateSnapshot()->getName() . '-> ' .$taxRate->getName();
                            }

                            return $taxRate->getName();
                        }
                    ]
                )
            );
        }

        if ($lineItem->isTaxRateAmountDiffrent()) {
            $form->add('taxRateSnapshotAmountOverwrite', CheckboxType::class, [
                'label' => 'owl.ui.invoice.line_item.tax_rate_amount_changed_overwrite',
                'mapped' => false,
                'required' => false,
            ]);
        }
    }

    public function preSubmit(FormEvent $formEvent): void
    {
        $data = $formEvent->getData();
        $form = $formEvent->getForm();
        /** @var LineItemInterface $lineItem */
        $lineItem = $form->getData();
        $taxRate = $lineItem?->getTaxRate();
        $taxRateSnapshot = $lineItem?->getTaxRateSnapshot();

        if ($taxRate === null || $taxRateSnapshot === null) {
            return;
        }

        if ($data['taxRate'] !== $taxRateSnapshot->getCode()) {
            $taxRateSnapshot->setAmount($lineItem->getTaxRateSnapshot()->getAmount());
            $taxRateSnapshot->setName($lineItem->getTaxRateSnapshot()->getName());

            return;
        }

        if (isset($data['taxRateSnapshotNameOverwrite']) && (int) $data['taxRateSnapshotNameOverwrite'] === 1) {
            $taxRateSnapshot->setName($lineItem->getTaxRate()->getName());
        }

        if (isset($data['taxRateSnapshotAmountOverwrite']) && (int) $data['taxRateSnapshotAmountOverwrite'] === 1) {
            $taxRateSnapshot->setAmount($lineItem->getTaxRate()->getAmount());
        }

        $lineItem->setTaxRateSnapshot($taxRateSnapshot);
    }

    public function submit(FormEvent $formEvent): void
    {
        $form = $formEvent->getForm();
        /** @var LineItemInterface $lineItem */
        $lineItem = $form->getData();
        $taxRate = $lineItem?->getTaxRate();
        $taxRateSnapshot = $lineItem?->getTaxRateSnapshot();

        if ($taxRate === null || $taxRateSnapshot === null) {
            return;
        }

        if ($taxRate->getCode() !== $taxRateSnapshot->getCode() && !$taxRateSnapshot->isNameChanged()) {
            $form->remove('taxRateSnapshotNameOverwrite');
        }

        if ($taxRate->getCode() !== $taxRateSnapshot->getCode() && !$taxRateSnapshot->isAmountChanged()) {
            $form->remove('taxRateSnapshotAmountOverwrite');
        }
    }
}
