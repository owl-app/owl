<?php

declare(strict_types=1);

namespace Owl\Bundle\AdminBundle\Form\EventSubscriber;

use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class TaxRateSnapshotSubscriber implements EventSubscriberInterface
{
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

        $snapshot = $lineItem->getTaxRateSnapshot();

        if ($lineItem->isTaxRateNameDiffrent()) {
            $form->add('snapshotNameOverwrite', CheckboxType::class, [
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
                        'choice_label' => function (TaxRateInterface $taxRate) use ($snapshot) {
                            if (
                                $taxRate->getCode() === $snapshot->getCode() && 
                                $taxRate->getName() !== $snapshot->getName()
                            ) {
                                return $snapshot->getName() . '-> ' .$taxRate->getName();
                            }

                            return $taxRate->getName();
                        }
                    ]
                )
            );
        }

        if ($lineItem->isTaxRateAmountDiffrent()) {
            $form->add('snapshotAmountOverwrite', CheckboxType::class, [
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
        $isChanged = false;

        if ($taxRate === null || $taxRateSnapshot === null || (isset($data['taxRate']) && $data['taxRate'] !== $taxRateSnapshot->getCode())) {
            return;
        }

        if (isset($data['snapshotNameOverwrite']) && (int) $data['snapshotNameOverwrite'] === 1) {
            $isChanged = true;
            $taxRateSnapshot->setName($lineItem->getTaxRate()->getName());
        }

        if (isset($data['snapshotAmountOverwrite']) && (int) $data['snapshotAmountOverwrite'] === 1) {
            $isChanged = true;
            $taxRateSnapshot->setAmount($lineItem->getTaxRate()->getAmount());
        }

        if ($isChanged) {
            $lineItem->setTaxRateSnapshot($taxRateSnapshot);
        }
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
            $form->remove('snapshotNameOverwrite');
        }

        if ($taxRate->getCode() !== $taxRateSnapshot->getCode() && !$taxRateSnapshot->isAmountChanged()) {
            $form->remove('snapshotAmountOverwrite');
        }
    }
}
